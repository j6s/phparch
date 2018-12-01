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
        $this->assertContains('Foo\Bar\ArgumentAnnotation', $this->extracted);
    }

    public function testExtractsImportedArgumentAnnotations(): void
    {
        $this->assertContains('Foo\Bar\ImportedArgumentAnnotation', $this->extracted);
    }

    public function testExtractsReturnTypeAnnotations(): void
    {
        $this->assertContains('Foo\Bar\ReturnAnnotation', $this->extracted);
    }

    public function testExtractsImportedReturnTypeAnnotations(): void
    {
        $this->assertContains('Foo\Bar\ImportedReturnAnnotation', $this->extracted);
    }


}
