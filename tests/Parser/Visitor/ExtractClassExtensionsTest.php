<?php
namespace J6s\PhpArch\Tests\Parser\Visitor;


use J6s\PhpArch\Parser\Visitor\ExtractClassExtensions;
use J6s\PhpArch\Tests\TestCase;

class ExtractClassExtensionsTest extends TestCase
{

    public function testShouldCollectClassExtensions(): void
    {
        $visitor = new ExtractClassExtensions();
        $this->parseAndTraverseFileContents(__DIR__ . '/Example/TestClass.php', $visitor);
        $this->assertContains(
            'Foo\\Bar\\ParentClass',
            $visitor->getNamespaces()
        );
    }

    public function testShouldCollectInterfaceImplementations(): void
    {
        $visitor = new ExtractClassExtensions();
        $this->parseAndTraverseFileContents(__DIR__ . '/Example/TestClass.php', $visitor);
        $this->assertContains(
            'Foo\\Bar\\SomeInterface',
            $visitor->getNamespaces()
        );
    }

}
