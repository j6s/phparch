<?php
namespace J6s\PhpArch\Tests\Component;


use J6s\PhpArch\Component\Component;
use J6s\PhpArch\Tests\TestCase;

class ComponentTest extends TestCase
{

    public function testComponentsCanHaveForbiddenDependencies(): void
    {
        // Component foo (Foo\..., Deep\Foo\...) must not depend on bar (Bar\..., Deep\Bar\...)
        $foo = new Component('foo');
        $bar = new Component('bar');

        $foo->addNamespace('Foo');
        $foo->addNamespace('Deep\\Foo');
        $bar->addNamespace('Bar');
        $bar->addNamespace('Deep\\Bar');

        $foo->mustNotDependOn($bar);

        $this->assertTrue($foo->isValidBetween('Foo\\Component', 'Deep\\Foo\\Another'));
        $this->assertTrue($foo->isValidBetween('Foo\\Component', 'Some\\Other\\Library'));
        $this->assertTrue($foo->isValidBetween('Deep\\Foo\\Component', 'Foo\\Another'));
        $this->assertTrue($foo->isValidBetween('Deep\\Foo\\Component', 'Some\\Other\\Library'));

        $this->assertFalse($foo->isValidBetween('Foo\\Component', 'Bar\\Component'));
        $this->assertFalse($foo->isValidBetween('Foo\\Component', 'Deep\\Bar\\Component'));
        $this->assertFalse($foo->isValidBetween('Deep\\Foo\\Component', 'Bar\\Component'));
        $this->assertFalse($foo->isValidBetween('Deep\\Foo\\Component', 'Deep\\Bar\\Component'));

        $this->assertTrue($foo->isValidBetween('Bar\\Component', 'Foo\\Component'));
    }

    public function testComponentCanBeDefinedToOnlyDefineOnAnotherComponent(): void
    {
        $http = new Component('AppHttp');
        $library = new Component('SymfonyHttp');

        $http->addNamespace('App\\Http');
        $library->addNamespace('Symfony\\Http');

        $http->mustOnlyDependOn($library);

        $this->assertTrue($http->isValidBetween('App\\Http\\AddArticleRequest', 'App\\Http\\Request'));
        $this->assertTrue($http->isValidBetween('App\\Http\\Request', 'Symfony\\Http\\Request'));
        $this->assertFalse($http->isValidBetween('App\\Http\\AddArticleRequest', 'App\\Controllers\\ArticleController'));
    }

}
