<?php
namespace J6s\PhpArch\Tests\Component;


use J6s\PhpArch\Component\Architecture;
use J6s\PhpArch\Tests\TestCase;

class ArchitectureTest extends TestCase
{

    public function testProvidesSpeakingApiForCreatingForbiddenDependencyConstraints(): void
    {
        $architecture = (new Architecture())
            ->component('foo')->identifiedByNamespace('Foo')
            ->mustNotDependOn('bar')->identifiedByNamespace('Bar');

        $this->assertTrue($architecture->isValidBetween('Foo\\Component', 'Foo\\Another'));
        $this->assertFalse($architecture->isValidBetween('Foo\\Component', 'Bar\\Component'));
        $this->assertTrue($architecture->isValidBetween('Foo\\Component', 'Outside\\Component'));
    }

    public function testProvidesSpeakingApiForCreatingInverseForbiddenDependencyConstraints(): void
    {
        $architecture = (new Architecture())
            ->component('bar')->identifiedByNamespace('Bar')
            ->mustNotBeDependedOnBy('foo')->identifiedByNamespace('Foo');

        $this->assertTrue($architecture->isValidBetween('Foo\\Component', 'Foo\\Another'));
        $this->assertFalse($architecture->isValidBetween('Foo\\Component', 'Bar\\Component'));
        $this->assertTrue($architecture->isValidBetween('Foo\\Component', 'Outside\\Component'));
    }

    public function testProvidesSpeakingApiForCreatingOnlyAllowedComponentConstraint(): void
    {
        $architecture = (new Architecture())
            ->component('foo')->identifiedByNamespace('Foo')
            ->mustOnlyDependOn('bar')->identifiedByNamespace('Bar');

        $this->assertTrue($architecture->isValidBetween('Foo\\Component', 'Foo\\Another'));
        $this->assertTrue($architecture->isValidBetween('Foo\\Component', 'Bar\\Component'));
        $this->assertFalse($architecture->isValidBetween('Foo\\Component', 'Outside\\Component'));
    }

    public function testAllowsChainingDependencyConstraints(): void
    {
        $architecture = (new Architecture())
            ->component('foo')->identifiedByNamespace('Foo')
            ->mustNotDependOn('bar')->identifiedByNamespace('Bar')
            ->andMustNotDependOn('baz')->identifiedByNamespace('Baz');

        $this->assertTrue($architecture->isValidBetween('Foo\\Component', 'Foo\\Another'));
        $this->assertFalse($architecture->isValidBetween('Foo\\Component', 'Bar\\Component'));
        $this->assertFalse($architecture->isValidBetween('Foo\\Component', 'Baz\\Component'));
        $this->assertTrue($architecture->isValidBetween('Bar\\Component', 'Baz\\Component'));
        $this->assertTrue($architecture->isValidBetween('Foo\\Component', 'Outside\\Component'));
    }

}
