<?php
namespace J6s\PhpArch\Parser;


use PhpParser\Node;

class InstanceCreationVisitor extends NamespaceCollectingVisitor
{

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\New_) {
            $this->namespaces[] = $node->class->toString();
        }
    }

}
