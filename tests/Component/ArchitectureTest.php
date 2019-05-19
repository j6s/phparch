<?php
namespace J6s\PhpArch\Tests\Component;


use J6s\PhpArch\Component\Architecture;
use J6s\PhpArch\Tests\Component\Example\Allowed\AllowedDependency;
use J6s\PhpArch\Tests\Component\Example\Forbidden\ForbiddenDependency;
use J6s\PhpArch\Tests\Component\Example\OutsideDependency;
use J6s\PhpArch\Tests\Component\Example\Test\InsideDependency;
use J6s\PhpArch\Tests\Component\Example\Test\TestClass;
use J6s\PhpArch\Tests\TestCase;

class ArchitectureTest extends TestCase
{
    const TEST_NAMESPACE = 'J6s\PhpArch\Tests\Component\Example\Test';
    const FORBIDDEN_NAMESPACE = 'J6s\PhpArch\Tests\Component\Example\Forbidden';
    const ALLOWED_NAMESPACE = 'J6s\PhpArch\Tests\Component\Example\Allowed';

    public function testProvidesSpeakingApiForCreatingForbiddenDependencyConstraints(): void
    {
        $architecture = (new Architecture())
            ->component('test')->identifiedByNamespace(static::TEST_NAMESPACE)
            ->mustNotDependOn('forbidden')->identifiedByNamespace(static::FORBIDDEN_NAMESPACE);

        $this->assertTrue($architecture->isValidBetween(TestClass::class, AllowedDependency::class));
        $this->assertFalse($architecture->isValidBetween(TestClass::class, ForbiddenDependency::class));
        $this->assertTrue($architecture->isValidBetween(TestClass::class, InsideDependency::class));
        $this->assertTrue($architecture->isValidBetween(TestClass::class, OutsideDependency::class));
    }

    public function testProvidesSpeakingApiForCreatingInverseForbiddenDependencyConstraints(): void
    {
        $architecture = (new Architecture())
            ->component('forbidden')->identifiedByNamespace(static::FORBIDDEN_NAMESPACE)
            ->mustNotBeDependedOnBy('test')->identifiedByNamespace(static::TEST_NAMESPACE);

        $this->assertTrue($architecture->isValidBetween(TestClass::class, AllowedDependency::class));
        $this->assertFalse($architecture->isValidBetween(TestClass::class, ForbiddenDependency::class));
        $this->assertTrue($architecture->isValidBetween(TestClass::class, InsideDependency::class));
        $this->assertTrue($architecture->isValidBetween(TestClass::class, OutsideDependency::class));
    }

    public function testProvidesSpeakingApiForCreatingOnlyAllowedComponentConstraint(): void
    {
        $architecture = (new Architecture())
            ->component('test')->identifiedByNamespace(static::TEST_NAMESPACE)
            ->mustOnlyDependOn('allowed')->identifiedByNamespace(static::ALLOWED_NAMESPACE);

        $this->assertTrue($architecture->isValidBetween(TestClass::class, AllowedDependency::class));
        $this->assertFalse($architecture->isValidBetween(TestClass::class, ForbiddenDependency::class));
        $this->assertTrue($architecture->isValidBetween(TestClass::class, InsideDependency::class));
        $this->assertFalse($architecture->isValidBetween(TestClass::class, OutsideDependency::class));
    }

    public function testAllowsChainingDependencyConstraints(): void
    {
        $architecture = (new Architecture())
            ->component('test')->identifiedByNamespace(static::TEST_NAMESPACE)
            ->mustOnlyDependOn('allowed')->identifiedByNamespace(static::ALLOWED_NAMESPACE)
            ->andMustNotBeDependedOnBy('forbidden')->identifiedByNamespace(static::FORBIDDEN_NAMESPACE);

        $this->assertTrue($architecture->isValidBetween(TestClass::class, AllowedDependency::class));
        $this->assertFalse($architecture->isValidBetween(TestClass::class, ForbiddenDependency::class));
        $this->assertTrue($architecture->isValidBetween(TestClass::class, InsideDependency::class));
        $this->assertFalse($architecture->isValidBetween(TestClass::class, OutsideDependency::class));
    }

}
