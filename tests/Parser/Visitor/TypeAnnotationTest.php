<?php
namespace J6s\PhpArch\Tests\Parser\Visitor;


use J6s\PhpArch\Parser\Visitor\TypeAnnotation;
use J6s\PhpArch\Tests\TestCase;

class TypeAnnotationTest extends TestCase
{

    /** @var string[] */
    protected $extracted;

    public function setUp()
    {
        parent::setUp();
        $visitor = new TypeAnnotation();
        $this->parseAndTraverseFileContents(__DIR__ . '/Example/TestClass.php', $visitor);
        $this->extracted = $visitor->getNamespaces();
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


}
