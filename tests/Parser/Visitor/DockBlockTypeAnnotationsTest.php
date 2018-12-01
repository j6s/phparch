<?php
namespace J6s\PhpArch\Tests\Parser\Visitor;


use J6s\PhpArch\Parser\Visitor\DocBlockTypeAnnotations;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockReturn;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedDocBlockArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedDocBlockReturn;
use J6s\PhpArch\Tests\TestCase;

class DockBlockTypeAnnotationsTest extends TestCase
{

    /** @var string[] */
    protected $extracted;

    public function setUp()
    {
        parent::setUp();
        $visitor = new DocBlockTypeAnnotations();
        $this->parseAndTraverseFileContents(__DIR__ . '/Example/TestClass.php', $visitor);
        $this->extracted = $visitor->getNamespaces();
    }


    public function testExtractsArgumentAnnotations(): void
    {
        $this->assertContains(DocBlockArgument::class, $this->extracted);
    }

    public function testExtractsImportedArgumentAnnotations(): void
    {
        $this->assertContains(DocBlockReturn::class, $this->extracted);
    }

    public function testExtractsReturnTypeAnnotations(): void
    {
        $this->assertContains(ImportedDocBlockArgument::class, $this->extracted);
    }

    public function testExtractsImportedReturnTypeAnnotations(): void
    {
        $this->assertContains(ImportedDocBlockReturn::class, $this->extracted);
    }

}
