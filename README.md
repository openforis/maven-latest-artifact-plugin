# maven-latest-artifact-plugin
Wordpress plugin to obtain the latest available version and publishing date of an artifact hosted in the Mave Central repository.


\=== Maven Latest Artifact URL Generator with Date ===

Contributors: Stefano Ricci, Alfonso Sanchez-Paus
Tags: maven, shortcode, artifact, version, date, repository
Requires at least: 4.7
Tested up to: 6.4
Requires PHP: 7.2
Stable tag: 1.1.0
License: GPLv2 or later
License URI: [MIT License](https://github.com/openforis/maven-latest-artifact-plugin/blob/master/LICENSE)

\== Description ==
Generates a direct URL to the latest (or latest release) Maven artifact for a given `groupId` and `artifactId`, including optional classifier and packaging, and displays the last updated date in a customizable format.

Supports caching results for 12 hours to reduce remote calls, and offers multiple output modes (`url`, `version`, `date`, `full`).

\== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install via the WordPress plugin installer.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the `[maven_latest_artifact]` shortcode in your posts or pages.

\== Shortcode Usage ==
Use the following attributes with the shortcode:

```
[maven_latest_artifact
    groupid="org.example.group"
    artifactid="artifact-name"
    classifier="sources"
    packaging="jar"
    repo_url="https://repo1.maven.org/maven2/"
    version_type="latest"
    output="url"
    date_format="Y-m-d"
]
```

| Attribute      | Default                           | Description                                |
| -------------- | --------------------------------- | ------------------------------------------ |
| `groupid`      | *required*                        | Maven groupId (dot-delimited)              |
| `artifactid`   | *required*                        | Maven artifactId                           |
| `classifier`   | (none)                            | Optional classifier suffix (e.g., sources) |
| `packaging`    | `jar`                             | Packaging type (jar, zip, war, pom)        |
| `repo_url`     | `https://repo1.maven.org/maven2/` | Base repository URL                        |
| `version_type` | `latest`                          | Use `latest` or `release`                  |
| `output`       | `url`                             | `url`, `version`, `date`, or `full`        |
| `date_format`  | `Y-m-d`                           | PHP date format for last-updated timestamp |

Examples:

* **URL only**: `[maven_latest_artifact groupid="org.example" artifactid="demo" output="url"]`
* **Version only**: `[maven_latest_artifact groupid="org.example" artifactid="demo" output="version"]`
* **Full info**: `[maven_latest_artifact groupid="org.example" artifactid="demo" output="full" date_format="F j, Y"]`

\== Frequently Asked Questions ==
\= Can I change the cache duration? =
The cache is set to 12 hours by default. To modify this, adjust the `set_transient()` duration in the plugin source.

\= Does this work with private repositories? =
Only public, unauthenticated Maven repositories are supported by default. For private repositories, you would need to extend the plugin to handle authentication.

\== Screenshots ==

1. Example of shortcode output displaying the artifact URL and date.

\== Changelog ==
\= 1.1.0 =

* Added shortcode output modes (`version`, `date`, `full`).
* Improved XML parsing and error handling.
* Implemented caching and customizable date format.

\= 1.0.0 =

* Initial release with basic URL generation for latest artifact.

\== Upgrade Notice ==
\= 1.1.0 =
Added multiple output options and caching. Shortcode attribute defaults unchanged.

