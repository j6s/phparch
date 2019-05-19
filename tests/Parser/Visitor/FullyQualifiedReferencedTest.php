<?php
namespace J6s\PhpArch\Tests\Parser\Visitor;

use J6s\PhpArch\Parser\Visitor\FullyQualifiedReference;
use J6s\PhpArch\Tests\Parser\Visitor\Example\InstanceCreation\ImportedInstanceCreation;
use J6s\PhpArch\Tests\Parser\Visitor\Example\ParentClass;
use J6s\PhpArch\Tests\Parser\Visitor\Example\SomeInterface;
use J6s\PhpArch\Tests\Parser\Visitor\Example\StaticMethodCall\ImportedStaticMethodCall;
use J6s\PhpArch\Tests\Parser\Visitor\Example\TestClass;
use J6s\PhpArch\Tests\Parser\Visitor\Example\Traits\ImporetdTrait;
use J6s\PhpArch\Tests\Parser\Visitor\Example\Traits\UsedTrait;
use J6s\PhpArch\Tests\TestCase;

class FullyQualifiedReferencedTest extends TestCase
{

    /** @var string[] */
    protected $extracted;

    public function setUp()
    {
        parent::setUp();
        $visitor = new FullyQualifiedReference();
        $this->parseAndTraverseFileContents(__DIR__ . '/Example/TestClass.php', $visitor);
        $this->extracted = $visitor->getNamespaces();
    }


    public function testShouldCollectClassExtensions(): void
    {
        $this->assertContains(ParentClass::class, $this->extracted);
    }

    public function testShouldCollectInterfaceImplementations(): void
    {
        $this->assertContains(SomeInterface::class, $this->extracted);
    }


    public function testShouldCollectInstanceCreation(): void
    {
        $this->assertContains(Example\InstanceCreation\InstanceCreation::class, $this->extracted);
    }

    public function testShouldCollectInstanceCreationOfImportedClasses(): void
    {
        $this->assertContains(ImportedInstanceCreation::class, $this->extracted);
    }


    public function testShouldCollectStaticMethodCalls(): void
    {
        $this->assertContains(Example\StaticMethodCall\StaticMethodCall::class, $this->extracted);
    }


    public function testShouldCollectStaticMethodCallsFromImportedClasses(): void
    {
        $this->assertContains(ImportedStaticMethodCall::class, $this->extracted);
    }

    public function testCollectsUsedTraits(): void
    {
        $this->assertContains(UsedTrait::class, $this->extracted);
    }

    public function testCollectsUsedImportedTraits(): void
    {
        $this->assertContains(ImporetdTrait::class, $this->extracted);
    }


    public function testExtractsArgumentAnnotations(): void
    {
        $this->assertContains(Example\TypeAnnotation\ArgumentAnnotation::class, $this->extracted);
    }

    public function testExtractsImportedArgumentAnnotations(): void
    {
        $this->assertContains(Example\TypeAnnotation\ImportedArgumentAnnotation::class, $this->extracted);
    }

    public function testExtractsReturnTypeAnnotations(): void
    {
        $this->assertContains(Example\TypeAnnotation\ReturnTypeAnnotation::class, $this->extracted);
    }

    public function testExtractsImportedReturnTypeAnnotations(): void
    {
        $this->assertContains(Example\TypeAnnotation\ImportedReturnTypeAnnotation::class, $this->extracted);
    }

    public function testIgnoresScalarTypeHints()
    {
        $this->assertNotContains('string', $this->extracted);
        $this->assertNotContains('int', $this->extracted);
    }

    public function testIgnoresSimpleFunctionCalls()
    {
        $this->assertNotContains('count', $this->extracted);
    }

    public function testIncludesNonExistingClasses()
    {
        $this->assertContains('Foo\Bar\This\Does\Not\Exist', $this->extracted);
    }

    public function testParsesReferencesToItselfCorrectly()
    {
        $this->assertContains(TestClass::class, $this->extracted);
    }

}
