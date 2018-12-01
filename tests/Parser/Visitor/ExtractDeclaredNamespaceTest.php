<?php
namespace J6s\PhpArch\Tests\Parser\Visitor;


use J6s\PhpArch\Parser\Visitor\ExtractDeclaredNamespace;
use J6s\PhpArch\Tests\TestCase;

class ExtractDeclaredNamespaceTest extends TestCase
{

    public function testExtractsDeclaredClass()
    {
        $visitor = new ExtractDeclaredNamespace();
        $this->parseAndTraverseFileContents(__DIR__ . '/Example/TestClass.php', $visitor);
        $this->assertEquals(
            'J6s\\PhpArch\\Tests\\Parser\\Visitor\\Example\\TestClass',
            $visitor->getDeclared()
        );
    }

}
