<?php
namespace J6s\PhpArch\Tests\Parser\Visitor;

use J6s\PhpArch\Parser\Visitor\StaticMethodCall;
use J6s\PhpArch\Tests\Parser\Visitor\Example\StaticMethodCall\ImportedStaticMethodCall;
use J6s\PhpArch\Tests\TestCase;

class StaticMethodCallTest extends TestCase
{

    public function testShouldCollectStaticMethodCalls(): void
    {
        $visitor = new StaticMethodCall();
        $this->parseAndTraverseFileContents(__DIR__ . '/Example/TestClass.php', $visitor);
        $this->assertContains(
            Example\StaticMethodCall\StaticMethodCall::class,
            $visitor->getNamespaces()
        );
    }


    public function testShouldCollectStaticMethodCallsFromImportedClasses(): void
    {
        $visitor = new StaticMethodCall();
        $this->parseAndTraverseFileContents(__DIR__ . '/Example/TestClass.php', $visitor);
        $this->assertContains(
            ImportedStaticMethodCall::class,
            $visitor->getNamespaces()
        );
    }

}
