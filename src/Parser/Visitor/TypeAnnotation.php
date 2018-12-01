<?php
namespace J6s\PhpArch\Parser\Visitor;


use PhpParser\Node;

class TypeAnnotation extends NamespaceCollectingVisitor
{
    public function enterNode(Node $node)
    {
        if (
            $node instanceof Node\Param &&
            $node->type &&
            $node->type->isFullyQualified()
        ) {
            $this->namespaces[] = $node->type->toString();
        }

        if (
            $node instanceof Node\Stmt\ClassMethod &&
            $node->returnType &&
            $node->returnType->isFullyQualified()
        ) {
            $this->namespaces[] = $node->returnType->toString();
        }
    }
}
