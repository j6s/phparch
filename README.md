# PHPArch

## What is this?

PHPArch is a work in progress architectural testing utility for PHP projects.
It is inspired by [archlint (C#)](https://gitlab.com/iternity/archlint.cs)
and [archunit (java)](https://github.com/TNG/ArchUnit).

## Example

```php
    public function testLibraryCodeDoesNotDependOnApplicationCode()
    {
        $errors = (new \J6s\PhpArch\PhpArch())
            ->fromDirectory(base_path('app'))
            ->validate(new ForbiddenDependency('Lib\\', 'App\\'))
            ->errors();
        $this->assertEmpty($errors);
    }
```

## Notes

This is only a rough implementation to see if this is even possible.

It is.
