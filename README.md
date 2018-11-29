# PHPArch

## What is this?

PHPArch is a work in progress architectural testing library for PHP projects.
It is inspired by [archlint (C#)](https://gitlab.com/iternity/archlint.cs)
and [archunit (java)](https://github.com/TNG/ArchUnit).

It can be used to help enforce architectural boundaries in an application in order
to prevent the architecture from rotting over time by introducing dependencies across
previously well defined architectural boundaries.

This library is strongly based on [mamuz/php-dependency-analysis](https://github.com/mamuz/PhpDependencyAnalysis)
which does all of the heavy lifting that is associated with analyzing the code. The two
libraries have different focuses though:
- phparch is focused on providing a testing utility to ensure architectural boundaries are kept.
- php-dependency-analysis can be used to visualize the components in your system and their dependencies.

## Examples

### Validation of simple namespaces

```php
    public function testSimpleNamespaces()
    {
        $errors = (new PhpArch())
            ->fromDirectory(__DIR__ . '/../../app')
            ->validate(new ForbiddenDependency('Lib\\', 'App\\'))
            ->validate(new MustBeSelfContained('App\\Utility'))
            ->errors();

        $this->assertEmpty($errors);
    }
```

### Validating an architecture

```php
    public function testArchitecture()
    {
        $architecture = (new Architecture())
            ->component('Components')
            ->identifiedByNamespace('J6s\\PhpArch\\Component')
            ->component('Validation')
            ->identifiedByNamespace('J6s\\PhpArch\\Validation')
            ->mustNotDependOn('Validation');
        
        $errors = (new PhpArch())
            ->fromDirectory(__DIR__ . '/../../app')
            ->validate($architecture)
            ->errors();

        $this->assertEmpty($errors);
    }
```

## Notes

This is only a rough implementation to see if this is even possible.

It is.

## TODO

- Document things
- Clean up API
- Add some more validation rules
- Add tests
- Publish to packagist
