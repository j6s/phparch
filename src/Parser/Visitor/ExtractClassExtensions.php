<?php
namespace J6s\PhpArch\Parser\Visitor;


use PhpParser\Node;

class ExtractClassExtensions extends NamespaceCollectingVisitor
{

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            if ($node->extends) {
                $this->namespaces[] = $node->extends->toString();
            }
            foreach ($node->implements as $implements) {
                $this->namespaces[] = $implements->toString();
            }
        }
    }
}
