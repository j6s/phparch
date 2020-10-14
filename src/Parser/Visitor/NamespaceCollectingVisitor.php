<?php
namespace J6s\PhpArch\Parser\Visitor;

use PhpParser\NodeVisitorAbstract;

abstract class NamespaceCollectingVisitor extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    protected $namespaces = [];

    public function getNamespaces(): array
    {
        return $this->namespaces;
    }
}
