<?php
namespace J6s\PhpArch\Parser\Visitor;

use PhpParser\Node;

class StaticMethodCall extends NamespaceCollectingVisitor
{

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\StaticCall && $node->class) {
            $this->namespaces[] = $node->class->toString();
        }
    }
}
