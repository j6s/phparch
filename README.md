# PHPArch [![Build Status](https://travis-ci.org/j6s/phparch.svg?branch=development)](https://travis-ci.org/j6s/phparch)

- [What is this?](#what-is-this)
- [Installation](#installation)
- [Simple Namespace validation](#simple-namespace-validation)
    - [Available Validators](#available-validators)
- [Defining an architecture](#defining-an-architecture)
    - [Syntactic sugar: Bulk definition of components](#syntactic-sugar-bulk-definition-of-components)
    - [Syntactic sugar: Chaining multiple dependency rules](#syntactic-sugar-chaining-multiple-dependency-rules)
- [Examples](#examples)
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
- `MustOnlyHaveAutoloadableDependencies` checks if all dependencies are autoloadable in the current environment.
  This can be helpful if two packages should not have any dependencies on each other but they still sneak in because
  the packages are often used together.
- `AllowInterfaces` is a wrapper for validators that allows dependencies if they are to interfaces.

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

### Syntactic sugar: Bulk definition of components

While the speaking Api for defining an architecture is great it can get convoluted and
hard to read if you have a lot of components. The `components` method can be used to define 
components using a simple associative array where the key is the component name and the
value is the namespaces that define the component. This way definitions of components and
setting up dependency rules can be split into 2 steps for better readability.

```php
// This
$architecture->components([
     'Foo' => 'Vendor\\Foo',
     'Bar' => [ 'Vendor\\Bar', 'Vendor\\Deep\\Bar' ]
]);

// Is the same as this
$architecture->component('Foo')
    ->identifiedByNamespace('Vendor\\Foo')
    ->component('Bar')
    ->identifierByNamespace('Vendor\\Bar')
    ->identifiedByNamespace('Vendor\\Deep\\Bar')
```

### Syntactic sugar: Chaining multiple dependency rules
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

## Examples

- [PHPArch tests it's own architecture](./tests/ArchitectureTest.php)
