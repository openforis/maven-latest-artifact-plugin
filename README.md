# Maven Latest Artifact URL Generator with Date

**Version:** 1.1.0
**Requires at least: 4.7
**Tested up to: 6.8
**Stable tag: 1.1.0
**Authors:** Stefano Ricci, Alfonso Sanchez-Paus
**License:** MIT
**Homepage:** [https://github.com/openforis/maven-latest-artifact-plugin](https://github.com/openforis/maven-latest-artifact-plugin)

---

A lightweight WordPress plugin that provides a `[maven_latest_artifact]` shortcode to fetch and display a direct URL (or version/date/full info) for the latest Maven artifact in any Maven repository. It also supports caching and customizable output formats.

## Features

* Retrieves **latest** or **latest release** version of any Maven artifact.
* Displays:

  * **URL** to the artifact
  * **Version** number only
  * **Last-updated date** in any PHP date format
  * **Full string**: version + last-updated date
* **Caching** of metadata for 12 hours to reduce remote requests.
* **Customizable** via shortcode attributes:

  * `groupid`, `artifactid`, `classifier`, `packaging`, `repo_url`, `version_type`, `output`, `date_format`.
* **No external dependencies**; uses WordPress HTTP API and core functions.

## Installation

1. Download the ZIP from the [releases page](https://github.com/yourname/maven-latest-artifact-url-generator/releases).
2. In your WordPress admin, go to **Plugins → Add New → Upload Plugin**.
3. Upload the ZIP, install, and activate.
4. Place the shortcode in your post or page.

## Usage

```php
// Basic URL output (default):
[maven_latest_artifact groupid="org.openforis.collect" artifactid="collect-installer"]

// Version only:
[maven_latest_artifact groupid="org.example" artifactid="demo" output="version"]

// Date only with custom format:
[maven_latest_artifact groupid="org.example" artifactid="demo" output="date" date_format="F j, Y"]

// Full info (version + date):
[maven_latest_artifact groupid="org.example" artifactid="demo" output="full"]
```

### Shortcode Attributes

| Attribute      | Default                           | Description                                     |
| -------------- | --------------------------------- | ----------------------------------------------- |
| `groupid`      | *required*                        | Maven `groupId` (e.g. `org.openforis.collect`)  |
| `artifactid`   | *required*                        | Maven `artifactId` (e.g. `collect-installer`)   |
| `classifier`   | (none)                            | Optional classifier (e.g. `sources`, `javadoc`) |
| `packaging`    | `jar`                             | Packaging type (`jar`, `zip`, `war`, `pom`)     |
| `repo_url`     | `https://repo1.maven.org/maven2/` | Base repository URL                             |
| `version_type` | `latest`                          | Use `latest` or `release`                       |
| `output`       | `url`                             | `url`, `version`, `date`, or `full`             |
| `date_format`  | `Y-m-d`                           | Any valid PHP `date()` format                   |

## Examples

* **Direct download link:**

  `[maven_latest_artifact groupid="com.example" artifactid="lib" packaging="zip" output="url"]`

* **Display only version:**

  `[maven_latest_artifact groupid="com.example" artifactid="lib" output="version"]`

* **Display formatted date:**

  `[maven_latest_artifact groupid="com.example" artifactid="lib" output="date" date_format="l, F j, Y"]`

* **Full combined output:**

  `[maven_latest_artifact groupid="org.openforis.collect" artifactid="collect-installer" output="full"]`

## Changelog

### 1.1.0

* Added `output="version"` and `output="date"` modes.
* Improved error handling and XML parsing.
* Caching implemented (12h via WP transients).

### 1.0.0

* Initial plugin release with basic `url` mode.

## Contributing

1. Fork the repo on GitHub.
2. Create a feature branch (`git checkout -b feature/foo`).
3. Commit your changes (`git commit -am 'Add foo feature'`).
4. Push to the branch (`git push origin feature/foo`).
5. Open a Pull Request.

## License

This plugin is licensed under the MIT License. See [LICENSE](https://github.com/openforis/maven-latest-artifact-plugin/blob/master/LICENSE) for details.
