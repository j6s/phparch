<?php
namespace J6s\PhpArch\Parser\Visitor;

use PhpParser\Node;

class UseStatement extends NamespaceCollectingVisitor
{

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\UseUse) {
            $this->namespaces[] = $node->name->toString();
        }
        return null;
    }
}
