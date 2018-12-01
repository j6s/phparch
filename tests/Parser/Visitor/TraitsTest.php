<?php
namespace J6s\PhpArch\Tests\Parser\Visitor;


use J6s\PhpArch\Parser\Visitor\Traits;
use J6s\PhpArch\Tests\Parser\Visitor\Example\Traits\ImporetdTrait;
use J6s\PhpArch\Tests\Parser\Visitor\Example\Traits\UsedTrait;
use J6s\PhpArch\Tests\TestCase;

class TraitsTest extends TestCase
{

    /** @var string[] */
    protected $extracted;

    public function setUp()
    {
        parent::setUp();
        $visitor = new Traits();
        $this->parseAndTraverseFileContents(__DIR__ . '/Example/TestClass.php', $visitor);
        $this->extracted = $visitor->getNamespaces();
    }

    public function testCollectsUsedTraits(): void
    {
        $this->assertContains(UsedTrait::class, $this->extracted);
    }

    public function testCollectsUsedImportedTraits(): void
    {
        $this->assertContains(ImporetdTrait::class, $this->extracted);
    }

}
