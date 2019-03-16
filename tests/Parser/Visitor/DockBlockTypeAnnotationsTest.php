<?php
namespace J6s\PhpArch\Tests\Parser\Visitor;


use J6s\PhpArch\Parser\Visitor\DocBlockTypeAnnotations;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\AnonymousClassDocBlockArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockReturn;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedAnonymousClassDocBlockArgument;
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

    public function testExtractsArgumentAnnotationsFromAnonymousClass()
    {
        $this->assertContains(AnonymousClassDocBlockArgument::class, $this->extracted);
    }

    public function testExtractsImportedArgumentAnnotationFromAnonymousClass()
    {
        $this->assertContains(ImportedAnonymousClassDocBlockArgument::class, $this->extracted);
    }

    public function testIgnoresScalarTypeHints()
    {
        $this->assertNotContains('string', $this->extracted);
        $this->assertNotContains('int', $this->extracted);
    }

    public function testIncludesNonExistingClasses()
    {
        $this->assertContains('Non\\ExistingClass', $this->extracted);
        $this->assertContains('Another\\Non\\ExistingClass', $this->extracted);
    }
}
