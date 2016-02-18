# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [0.2.3] - 2016-02-18
### Added
- `ShortcodeManager` now properly manages passing `$context` information to its shortcodes.

### Fixed
- Docblock fixes for `ShortcodeManager::__construct()`.

## [0.2.2] - 2016-02-18
### Fixed
- `DependencyManager` is now optional and you can pass in `null` or omit the argument completely.

## [0.2.1] - 2016-02-18
### Fixed
- Make shortcodes work by fixing the config routing.
- Make shortcode UI work by fixing the config routing.
- Let array_walk always work with references.

## [0.2.0] - 2016-02-18
### Fixed
- Switched to v0.2+ for both `brightnucleus/config` & `brightnucleus/dependencies`.
- Removed `$config_key` from constructor and from `processConfig()`.

## [0.1.2] - 2016-02-17
### Fixed
- Tweak precommit script.
- Updated copyright dates.
- Several typehint tweaks.

## [0.1.1] - 2016-02-17
### Fixed
- Fixed badges in readme.
- Fixed changelog.

## [0.1.0] - 2016-02-17
### Added
- Initial release to GitHub.

[0.2.3]: https://github.com/brightnucleus/shortcodes/compare/v0.2.2...v0.2.3
[0.2.2]: https://github.com/brightnucleus/shortcodes/compare/v0.2.1...v0.2.2
[0.2.1]: https://github.com/brightnucleus/shortcodes/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/brightnucleus/shortcodes/compare/v0.1.2...v0.2.0
[0.1.2]: https://github.com/brightnucleus/shortcodes/compare/v0.1.1...v0.1.2
[0.1.1]: https://github.com/brightnucleus/shortcodes/compare/v0.1.0...v0.1.1
[0.1.0]: https://github.com/brightnucleus/shortcodes/compare/v0.0.0...v0.1.0
