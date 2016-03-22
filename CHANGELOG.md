# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Fixed
- Fix code style issue.

## [0.2.14] - 2016-03-22
### Fixed
- Only declare `do_tag()` function if it does not exist already.

## [0.2.13] - 2016-03-22
### Fixed
- Switch `beberlei/assert` back to official branch. Issue [#138](https://github.com/beberlei/assert/issues/138) has been fixed with v2.5.

## [0.2.12] - 2016-03-04
### Fixed
- Pass `$atts` through to view so they can be used in there. Props to @GaryJones.

## [0.2.11] - 2016-03-04
### Fixed
- Switch `beberlei/assert` to own fork until [#138](https://github.com/beberlei/assert/issues/138) has been fixed.

## [0.2.10] - 2016-02-26
### Fixed
- Removed executable bit from files.
- Correctly type-hint closures.
- Added abstract method declarations to `CheckNeedTrait` to ensure that classes using it will provide the necessary methods.
- Bump `brightnucleus/config` requirement to v0.2.4

## [0.2.9] - 2016-02-25
### Added
- Enqueueing of dependencies for a shortcode now falls back to dependencies already registered outside of `DependencyManager`.
- Added `CheckNeedTrait` to deduplicate `is_needed()` method.

### Fixed
- Corrected end-of-line characters for several files.

## [0.2.8] - 2016-02-25
### Fixed
- Use stable version of `brightnucleus/dependencies`.

## [0.2.7] - 2016-02-25
### Changed
- Dependencies are only enqueued if their handles are included in the shortcode's "dependencies" key.

### Fixed
- The `ShortcodeManagerInterface` now reuses `Registerable`.

## [0.2.6] - 2016-02-24
### Added
- `gamajo/TemplateLoader` is being used in a new `TemplatedShortcode` class to let themes override the shortcode's views.

### Fixed
- Let `is_needed` default to `true` so that it can be omited in most cases in the config.

## [0.2.5] - 2016-02-22
### Fixed
- Use shortcode tag getter method to access tag in `Shortcode::do_this()` method.
- Docblock tweaks.

## [0.2.4] - 2016-02-22
### Added
- Added `ShortcodeManagerInterface` to decouple the implementation from the software that uses it.
- Added `do_tag()` function to execute shortcode tags directly. Works with external shortcodes too.
- Added `ShortcodeManagerInterface::do_tag()` method as a convenience access to `do_tag()` function.
- Added `ShortcodeInterface::do_this()` method as a convenience access to `do_tag()` function.

### Fixed
- `init_shortcodes()` is now protected instead of public.

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

[0.2.14]: https://github.com/brightnucleus/shortcodes/compare/v0.2.13...v0.2.14
[0.2.13]: https://github.com/brightnucleus/shortcodes/compare/v0.2.12...v0.2.13
[0.2.12]: https://github.com/brightnucleus/shortcodes/compare/v0.2.11...v0.2.12
[0.2.11]: https://github.com/brightnucleus/shortcodes/compare/v0.2.10...v0.2.11
[0.2.10]: https://github.com/brightnucleus/shortcodes/compare/v0.2.9...v0.2.10
[0.2.9]: https://github.com/brightnucleus/shortcodes/compare/v0.2.8...v0.2.9
[0.2.8]: https://github.com/brightnucleus/shortcodes/compare/v0.2.7...v0.2.8
[0.2.7]: https://github.com/brightnucleus/shortcodes/compare/v0.2.6...v0.2.7
[0.2.6]: https://github.com/brightnucleus/shortcodes/compare/v0.2.5...v0.2.6
[0.2.5]: https://github.com/brightnucleus/shortcodes/compare/v0.2.4...v0.2.5
[0.2.4]: https://github.com/brightnucleus/shortcodes/compare/v0.2.3...v0.2.4
[0.2.3]: https://github.com/brightnucleus/shortcodes/compare/v0.2.2...v0.2.3
[0.2.2]: https://github.com/brightnucleus/shortcodes/compare/v0.2.1...v0.2.2
[0.2.1]: https://github.com/brightnucleus/shortcodes/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/brightnucleus/shortcodes/compare/v0.1.2...v0.2.0
[0.1.2]: https://github.com/brightnucleus/shortcodes/compare/v0.1.1...v0.1.2
[0.1.1]: https://github.com/brightnucleus/shortcodes/compare/v0.1.0...v0.1.1
[0.1.0]: https://github.com/brightnucleus/shortcodes/compare/v0.0.0...v0.1.0
