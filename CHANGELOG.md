# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.0] - 2018-11-30
### Added
- New `assertHasNoErrors` method was added to make checking for architecture violations in PHPUnit easier.

### Changed
- Switched from using php-dependency-analysis to manually using `nikic/php-parser` - phpda uses outdated dependencies
  that are hard to install on many up-to-date systems (such as a current laravel installation).

## [0.1.0] - 2018-11-29
### Added
- Initial release. Literally everything was added.
