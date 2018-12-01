<?php
namespace J6s\PhpArch\Parser\Visitor;


use PhpParser\Node;

class Traits extends NamespaceCollectingVisitor
{

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\TraitUse) {
            foreach ($node->traits as $trait) {
                $this->namespaces[] = (string) $trait;
            }
        }
    }

}
