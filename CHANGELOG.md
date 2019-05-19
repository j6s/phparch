# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2019-05-19
### Added
- A new `MustOnlyHaveAutoloadableDependencies` validator has been added in order to prevent accidental dependencies
  to unrelated packages that just happen to be used in the same system often.
    - All components in architectures are now being checked against this new validator.
- A new `MustOnlyDependOnComposerDependencies` validator has been added in order to prevent accidentally using
  namespaces that are not also declared in `composer.json`.
- A new `ExplicitlyAllowDependency` validator allows explicitly allowing dependencies from one component to another.
- Architectures now have a bunch of new helper methods
    - `mustOnlyDependOnComposerDependencies` adds `MustOnlyDependOnComposerDependencies` validator.
    - `addComposerBasedComponent` initializes a full component from the given `composer.json` file and
      adds a `MustOnlyDependOnComposerDependencies` validator
    - `isAllowedToDependOn` allows dependencies from one component to another one.
    - `disallowInterdependence` makes it easy to disallow dependence between many different components.
    - `mustNotDependOnAnyOtherComponent` makes it easy to declare core components that should not depend on
      anything else that is architecturally significant.
    
### Removed
- Support for PHP 7.1 was dropped

## [1.0.0] - 2019-02-18
### Added
- Allowing dependencies to Interfaces only is now possible
    - Using the `AllowInterfaces` Validation wrapper
    - Using the `mustNotDependOn` method on a component
    - Using the `mustNotDirectlyDependOn` method on an architecture
- Bulk declaration of components using the `Architecture->components` method is now possible

### Fixed
- Dependencies of anonymous / inner classes are now correctly tracked


## [0.3.1] - 2018-12-12
### Fixed
- Not loading tests into production autoloader anymore
- Now ignoring references to non-existent classes
- Fixed Validators not being able to be serialized correctly for error output


## [0.3.0] - 2018-12-01
### Added
- Now correctly identifies the following types of dependencies (though in most cases they have already been tracked through `use` statements):
    - DocBlock comments
    - Static method calls
    - Argument type annotations
    - Return type annotations

### Fixed
- Namespace comparisons against higher up namespaces no longer match


## [0.2.0] - 2018-11-30
### Added
- New `assertHasNoErrors` method was added to make checking for architecture violations in PHPUnit easier.

### Changed
- Switched from using php-dependency-analysis to manually using `nikic/php-parser` - phpda uses outdated dependencies
  that are hard to install on many up-to-date systems (such as a current laravel installation).


## [0.1.0] - 2018-11-29
### Added
- Initial release. Literally everything was added.
