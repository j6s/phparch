<?php
namespace J6s\PhpArch\Tests\Parser\Visitor;


use J6s\PhpArch\Parser\Visitor\UseStatement;
use J6s\PhpArch\Tests\TestCase;

class UseStatementTest extends TestCase
{

    public function testShouldCollectUseStatements(): void
    {
        $visitor = new UseStatement();
        $this->parseAndTraverseFileContents(__DIR__ . '/Example/TestClass.php', $visitor);
        $this->assertContains(
            'Foo\\Bar\\Baz',
            $visitor->getNamespaces()
        );
    }

}
