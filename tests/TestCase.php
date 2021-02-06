<?php
namespace J6s\PhpArch\Tests;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;

/**
 * This class is part of PHParchs own test suite that tests the library
 * and is not meant to be used in projects.
 */
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
