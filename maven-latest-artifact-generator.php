<?php
/**
 * Plugin Name: Maven Latest Artifact URL Generator with Date
 * Plugin URI:  https://github.com/yourname/maven-latest-artifact-url-generator
 * Description: Generates a URL to the latest Maven artifact, including its version and last updated date.
 * Version:     1.1.0
 * Author:      Stefano Ricci, Alfonso Sanchez-Paus
 * Author URI:  https://openforis.org
 * License:     MIT
 * License URI: https://github.com/openforis/maven-latest-artifact-plugin/blob/master/LICENSE
 * Text Domain: maven-latest-artifact-url-generator
 */

// Register the shortcode [maven_latest_artifact]
function maven_latest_artifact_shortcode($atts) {
    // Define default attributes for shortcode
    $a = shortcode_atts(array(
        'groupid'      => '',      // Maven groupId (e.g., org.openforis.collect)
        'artifactid'   => '',      // Maven artifactId (e.g., collect-installer)
        'classifier'   => '',      // Optional classifier (sources, javadoc)
        'packaging'    => 'jar',   // Packaging type (jar, zip, war, pom)
        'repo_url'     => 'https://repo1.maven.org/maven2/', // Base repository URL
        'version_type' => 'latest',// Choose between 'latest' or 'release'
        'output'       => 'url',   // Output mode: 'url', 'version', 'date', 'full'
        'date_format'  => 'Y-m-d', // PHP date() format
    ), $atts);

    // Sanitize and validate inputs to prevent XSS and invalid data
    $groupid    = sanitize_text_field($a['groupid']);
    $artifactid = sanitize_text_field($a['artifactid']);
    $classifier = !empty($a['classifier'])
                  ? '-' . sanitize_text_field($a['classifier'])
                  : '';
    $packaging  = sanitize_text_field($a['packaging']);
    $repo_url   = esc_url_raw($a['repo_url']);
    $version_type = in_array($a['version_type'], ['latest','release'])
                  ? $a['version_type']
                  : 'latest';
    $output     = in_array($a['output'], ['url','version','date','full'])
                  ? $a['output']
                  : 'url';
    $date_format = sanitize_text_field($a['date_format']);

    // Required parameters check
    if (empty($groupid) || empty($artifactid)) {
        return 'Error: groupid and artifactid are required parameters for the Maven Latest Artifact shortcode.';
    }

    // Convert groupId dot notation to URL path (dots -> slashes)
    $groupid_path = str_replace('.', '/', $groupid);

    // Build metadata URL for maven-metadata.xml
    $metadata_url = rtrim($repo_url, '/')
                  . '/' . $groupid_path
                  . '/' . $artifactid
                  . '/maven-metadata.xml';

    // Prepare cache key and attempt to retrieve cached data
    $cache_key = 'maven_artifact_data_' . md5(serialize($a));
    $cached_data = get_transient($cache_key);

    if ($cached_data !== false) {
        // Use cache if available
        $latest_version = $cached_data['version'];
        $last_updated_timestamp = $cached_data['timestamp'];
    } else {
        // Fetch metadata from remote repository
        $response = wp_remote_get($metadata_url);
        if (is_wp_error($response)) {
            // Return error message if HTTP request fails
            return 'Error fetching Maven metadata: ' . $response->get_error_message();
        }
        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            // Handle empty response
            return 'Error: Empty response when fetching Maven metadata from ' . esc_html($metadata_url);
        }

        // Parse XML safely
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);
        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            // Aggregate XML errors
            $msgs = array_map(function($err) { return $err->message; }, $errors);
            return 'Error parsing Maven metadata XML. Errors: ' . implode(', ', $msgs);
        }

        // Extract latest or release version and raw timestamp
        $latest_version = isset($xml->versioning->{$version_type})
                         ? (string)$xml->versioning->{$version_type}
                         : '';
        $raw_last_updated = isset($xml->versioning->lastUpdated)
                          ? (string)$xml->versioning->lastUpdated
                          : '';

        if (empty($latest_version)) {
            // Handle missing version
            return 'Could not find ' . esc_html($version_type)
                   . ' version for ' . esc_html($groupid)
                   . ':' . esc_html($artifactid) . '.';
        }

        // Convert Maven timestamp (YYYYMMDDHHmmss) to Unix timestamp
        $last_updated_timestamp = false;
        if (!empty($raw_last_updated)) {
            // Check if the timestamp is exactly 14 digits (expected format)
            if (preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/', $raw_last_updated, $matches)) {
                $year = $matches[1];
                $month = $matches[2];
                $day = $matches[3];
                $hour = $matches[4];
                $minute = $matches[5];
                $second = $matches[6];
                // Create a DateTime object and set the timezone to UTC before converting to timestamp
                // Using DateTime::createFromFormat for more robust parsing
                $datetime_utc = DateTime::createFromFormat('YmdHis', $raw_last_updated, new DateTimeZone('UTC'));
                if ($datetime_utc) {
                    $last_updated_timestamp = $datetime_utc->getTimestamp();
                }
            }
        }

    // Format the date for output or show 'N/A' if unavailable
    $formatted_date = ($last_updated_timestamp !== false)
                    ? date_i18n($date_format, $last_updated_timestamp)
                    : 'N/A';

    // Construct the final artifact download URL
    $artifact_url = rtrim($repo_url, '/')
                  . '/' . $groupid_path
                  . '/' . $artifactid
                  . '/' . $latest_version
                  . '/' . $artifactid
                  . '-' . $latest_version
                  . $classifier
                  . '.' . $packaging;

    // Output based on requested output type
    switch ($output) {
        case 'version':
            return esc_html($latest_version);
        case 'date':
            return esc_html($formatted_date);
        case 'full':
            return 'Latest Version: ' . esc_html($latest_version)
                 . ' (Last Updated: ' . esc_html($formatted_date) . ')';
        case 'url':
        default:
            return esc_url($artifact_url);
    }
}

// Hook shortcode into WordPress
add_shortcode('maven_latest_artifact', 'maven_latest_artifact_shortcode');
