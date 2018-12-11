# PHPArch [![Build Status](https://travis-ci.org/j6s/phparch.svg?branch=development)](https://travis-ci.org/j6s/phparch)

- [What is this?](#what-is-this)
- [Installation](#installation)
- [Simple Namespace validation](#simple-namespace-validation)
    - [Available Validators](#available-validators)
- [Defining an architecture](#defining-an-architecture)

## What is this?

PHPArch is a work in progress architectural testing library for PHP projects.
It is inspired by [archlint (C#)](https://gitlab.com/iternity/archlint.cs)
and [archunit (java)](https://github.com/TNG/ArchUnit).

It can be used to help enforce architectural boundaries in an application in order
to prevent the architecture from rotting over time by introducing dependencies across
previously well defined architectural boundaries.

## Installation

You can install PHPArch using composer.
If you don't know what composer is then you probably don't need a library for architectural testing.

```bash
$ composer require j6s/phparch
```

## Simple Namespace validation

The most simple type of check PHPArch can help you with are simple namespace based checks:
Setup rules for which namespace is allowed for forbidden to depend on which other namespace.

```php
public function testSimpleNamespaces()
{
    (new PhpArch())
        ->fromDirectory(__DIR__ . '/../../app')
        ->validate(new ForbiddenDependency('Lib\\', 'App\\'))
        ->validate(new MustBeSelfContained('App\\Utility'))
        ->validate(new MustOnlyDependOn('App\\Mailing', 'PHPMailer\\PHPMailer'))
        ->assertHasNoErrors();
}
```

### Available Validators
Currently the following validators are available:
- `ForbiddenDependency` Lets you declare that one namespace is not allowed to depend on another namespace.
- `MustBeSelfContained` Lets you declare that a namespace must be self-contained meaning that it may not have
  any external dependencies.
- `MustOnlyDependOn` Lets you declare that one namespace must only depend on another namespace.

Most architectural boundaries can be described with these rules.

## Defining an architecture

PHPArch also contains a fluent API that allows you to define a component based Architecture which is then validated.
The API is based on components which are identified by one or more namespaces instead of Layers or 'Onion Peels' because
it is the simplest way to communicate any architecture - no matter what the implementation details of it are.

```php
public function testArchitecture()
{
    $architecture = (new Architecture())
        ->component('Components')->identifiedByNamespace('J6s\\PhpArch\\Component')
        ->mustNotDependOn('Validation')->identifiedByNamespace('J6s\\PhpArch\\Validation');
    
    (new PhpArch())
        ->fromDirectory(__DIR__ . '/../../app')
        ->validate($architecture)
        ->assertHasNoErrors();
}
```

Most of defining an architecture is only syntactic sugar over the namespace validators above.
The following methods allow you to add assertions to your component structure:

- `mustNotDependOn`
- `mustNotBeDependedOnBy`
- `mustOnlyDependOn`

If a non-existing component is referenced in one of these methods then it will be created.
These methods will also set the referenced component as the currently active one - so when using
`->mustNotDependOn('FooBar')` all future operations reference the `FooBar` component.

In order to chain multiple dependency rules for a single component there are some convinience
methods available:

- `andMustNotDependOn`
- `andMustNotBeDependedOnBy`

```php
// This
(new Architecture)
    ->component('Foo')
    ->mustNotDependOn('Bar')
    ->andMustNotDependOn('Baz')

// Is this same as this:
(new Architecture())
    ->component('Foo')->mustNotDependOn('Bar')
    ->component('Foo')->mustNotDependOn('Baz')
```
