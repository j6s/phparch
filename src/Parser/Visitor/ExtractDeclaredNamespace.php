<?php
namespace J6s\PhpArch\Parser\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ExtractDeclaredNamespace extends NodeVisitorAbstract
{

    private string $declared = '';

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassLike && isset($node->namespacedName) && $node->namespacedName  !== null) {
            $this->declared = $node->namespacedName->toString();
        }
        return null;
    }

    public function declaresNamespace(): bool
    {
        return $this->declared !== '';
    }

    public function getDeclared(): string
    {
        return trim($this->declared, '\\');
    }
}
