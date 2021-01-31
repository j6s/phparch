<?php declare(strict_types=1);

namespace J6s\PhpArch\Parser\Visitor;

use PhpParser\NodeVisitorAbstract;

abstract class NamespaceCollectingVisitor extends NodeVisitorAbstract
{
    /** @var string[] */
    protected array $namespaces = [];

    /** @return string[] */
    public function getNamespaces(): array
    {
        return $this->namespaces;
    }
}
