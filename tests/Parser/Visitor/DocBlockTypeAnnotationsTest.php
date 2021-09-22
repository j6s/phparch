<?php
namespace J6s\PhpArch\Tests\Parser\Visitor;


use J6s\PhpArch\Parser\Visitor\DocBlockTypeAnnotations;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\AnonymousClassDocBlockArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockArrayItem;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockGenericTypeArrayLong;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockGenericTypeArrayShort;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockReturn;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockTypedTemplate;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionGenericChildA;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionGenericChildB;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionGenericChildC;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionGenericChildD;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionGenericWrapperA;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionGenericWrapperB;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionTypeA;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionTypeB;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\GenericPseudoType;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedAnonymousClassDocBlockArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedDocBlockArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedDocBlockReturn;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedGenericArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedGenericClassArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\TestClass;
use J6s\PhpArch\Tests\TestCase;

class DocBlockTypeAnnotationsTest extends TestCase
{

    /** @var string[] */
    protected $extracted;

    public function setUp(): void
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

    public function testParsesReferencesToItselfCorrectly()
    {
        $this->assertContains(TestClass::class, $this->extracted);
    }

    public function testIncludesGenericClasses()
    {
        $this->assertContains(ImportedGenericClassArgument::class, $this->extracted);
        $this->assertContains(ImportedGenericArgument::class, $this->extracted);
    }

    public function testIncludesUnionTypesFromDocBlocks(): void
    {
        $this->assertContains(DocBlockUnionTypeA::class, $this->extracted);
        $this->assertContains(DocBlockUnionTypeB::class, $this->extracted);
    }

    public function testResolvesUnionsWhenCombinedWithGenerics(): void
    {
        $this->assertContains(DocBlockUnionGenericWrapperA::class, $this->extracted);
        $this->assertContains(DocBlockUnionGenericWrapperB::class, $this->extracted);
        $this->assertContains(DocBlockUnionGenericChildA::class, $this->extracted);
        $this->assertContains(DocBlockUnionGenericChildB::class, $this->extracted);
        $this->assertContains(DocBlockUnionGenericChildC::class, $this->extracted);
        $this->assertContains(DocBlockUnionGenericChildD::class, $this->extracted);
    }

    public function testResolvesArrayTypes(): void
    {
        $this->assertContains(DocBlockArrayItem::class, $this->extracted);
    }

    public function testIgnoresUntypedTemplatesInDocBlocks(): void
    {
        $this->assertNotContains('UntypedTemplateInDocblock', $this->extracted);
    }

    public function testResolvesTypedTemplateTagsToBaseType(): void
    {
        $this->assertNotContains('TypedTemplateInDocblock', $this->extracted);
        $this->assertContains(DocBlockTypedTemplate::class, $this->extracted);
    }

    public function testResolvesShortStyleGenericShortArraySyntax(): void
    {
        $this->assertContains(DocBlockGenericTypeArrayShort::class, $this->extracted);
    }

    public function testResolvesShortStyleGenericLongArraySyntax(): void
    {
        $this->assertContains(DocBlockGenericTypeArrayLong::class, $this->extracted);
    }

    public function testResolvesGenericPseudoTypes(): void
    {
        $this->assertContains(GenericPseudoType::class, $this->extracted);
    }
}
