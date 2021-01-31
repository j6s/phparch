<?php
namespace J6s\PhpArch\Parser\Visitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\UseUse;

class UseStatement extends NamespaceCollectingVisitor
{

    public function enterNode(Node $node)
    {
        if ($node instanceof UseUse) {
            $this->namespaces[] = $node->name->toString();
        }
        return null;
    }
}
