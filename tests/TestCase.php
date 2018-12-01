<?php
namespace J6s\PhpArch\Tests;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;

class TestCase extends \PHPUnit\Framework\TestCase
{

    protected function parsePhpFileContents(string $fileName): array
    {
        return (new ParserFactory())
            ->create(ParserFactory::PREFER_PHP7)
            ->parse(file_get_contents($fileName));
    }

    protected function parseAndTraverseFileContents(string $fileName, NodeVisitor $visitor): void
    {
        $ast = $this->parsePhpFileContents($fileName);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);
    }

}
