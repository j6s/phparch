<?php
namespace J6s\PhpArch\Parser;


use J6s\PhpArch\Parser\Visitor\DocBlockTypeAnnotations;
use J6s\PhpArch\Parser\Visitor\ExtractDeclaredNamespace;
use J6s\PhpArch\Parser\Visitor\FullyQualifiedReference;
use J6s\PhpArch\Parser\Visitor\NamespaceCollectingVisitor;
use J6s\PhpArch\Parser\Visitor\UseStatement;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;

class Parser
{

    /** @var string */
    private $declaredNamespace;

    /** @var string[] */
    private $usedNamespaces = [];

    public function getDeclaredNamespace(): string
    {
        return $this->declaredNamespace;
    }

    public function getUsedNamespaces(): array
    {
        return $this->usedNamespaces;
    }


    public function process(array $ast): void
    {
        $declared = new ExtractDeclaredNamespace();
        $used = $this->usageExtractors();

        $this->traverseWithVisitors($ast, array_merge($used, [ $declared ]));

        $this->declaredNamespace = $declared->getDeclared();
        foreach ($used as $visitor) {
            foreach ($visitor->getNamespaces() as $namespace) {
                if (!\in_array($namespace, $this->usedNamespaces, true)) {
                    $this->usedNamespaces[] = $namespace;
                }
            }
        }
    }

    private function traverseWithVisitors(array $ast, array $visitors): void
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        foreach ($visitors as $visitor) {
            $traverser->addVisitor($visitor);
        }
        $traverser->traverse($ast);
    }

    /**
     * @return NamespaceCollectingVisitor[]
     */
    private function usageExtractors(): array {
        return [
            new FullyQualifiedReference(),
            new UseStatement(),
            new DocBlockTypeAnnotations(),
        ];
    }

}
