<?php
namespace J6s\PhpArch\Parser;


use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class UseStatement extends NamespaceCollectingVisitor
{

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\UseUse) {
            $this->namespaces[] = $node->name->toString();
        }
    }
}
