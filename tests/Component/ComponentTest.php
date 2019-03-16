<?php
namespace J6s\PhpArch\Tests\Component;


use J6s\PhpArch\Component\Component;
use J6s\PhpArch\Tests\Component\Example\Allowed\AllowedDependency;
use J6s\PhpArch\Tests\Component\Example\Forbidden\ForbiddenDependency;
use J6s\PhpArch\Tests\Component\Example\OutsideDependency;
use J6s\PhpArch\Tests\Component\Example\Test\InsideDependency;
use J6s\PhpArch\Tests\Component\Example\Test\TestClass;
use J6s\PhpArch\Tests\TestCase;

class ComponentTest extends TestCase
{
    const TEST_NAMESPACE = 'J6s\PhpArch\Tests\Component\Example\Test';
    const FORBIDDEN_NAMESPACE = 'J6s\PhpArch\Tests\Component\Example\Forbidden';
    const ALLOWED_NAMESPACE = 'J6s\PhpArch\Tests\Component\Example\Allowed';

    public function testComponentsCanHaveForbiddenDependencies(): void
    {
        // Component foo (Foo\..., Deep\Foo\...) must not depend on bar (Bar\..., Deep\Bar\...)
        $test = new Component('test');
        $forbidden = new Component('forbidden');

        $test->addNamespace(static::TEST_NAMESPACE);
        $forbidden->addNamespace(static::FORBIDDEN_NAMESPACE);

        $test->mustNotDependOn($forbidden);

        $this->assertTrue($test->isValidBetween(TestClass::class, AllowedDependency::class));
        $this->assertFalse($test->isValidBetween(TestClass::class, ForbiddenDependency::class));
        $this->assertTrue($test->isValidBetween(TestClass::class, InsideDependency::class));
        $this->assertTrue($test->isValidBetween(TestClass::class, OutsideDependency::class));
    }

    public function testComponentCanBeDefinedToOnlyDependOnAnotherComponent(): void
    {
        $test = new Component('test');
        $allowed = new Component('allowed');

        $test->addNamespace(static::TEST_NAMESPACE);
        $allowed->addNamespace(static::ALLOWED_NAMESPACE);

        $test->mustOnlyDependOn($allowed);

        $this->assertTrue($test->isValidBetween(TestClass::class, AllowedDependency::class));
        $this->assertFalse($test->isValidBetween(TestClass::class, ForbiddenDependency::class));
        $this->assertTrue($test->isValidBetween(TestClass::class, InsideDependency::class));
        $this->assertFalse($test->isValidBetween(TestClass::class, OutsideDependency::class));
    }

}
