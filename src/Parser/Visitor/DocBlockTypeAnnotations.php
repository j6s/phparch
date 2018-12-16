<?php
namespace J6s\PhpArch\Parser\Visitor;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use PhpParser\Node;
use ReflectionMethod;

class DocBlockTypeAnnotations extends NamespaceCollectingVisitor
{

    /** @var string */
    private $lastClass;

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassLike && $node->namespacedName !== null) {
            $this->lastClass = $node->namespacedName->toString();
        } elseif ($node instanceof Node\Stmt\ClassMethod && $this->lastClass) {
            $contextFactory = new \phpDocumentor\Reflection\Types\ContextFactory();
            $context = $contextFactory->createFromReflector(
                new ReflectionMethod($this->lastClass, (string) $node->name)
            );
            if ($node->hasAttribute('comments')) {
                $this->extractDocBlocks((array)$node->getAttribute('comments'), $context);
            }
        }
    }

    private function extractDocBlocks(array $docBlocks, Context $context): void
    {
        $factory  = \phpDocumentor\Reflection\DocBlockFactory::createInstance();

        foreach ($docBlocks as $docBlockString) {
            $docBlock = $factory->create((string) $docBlockString);

            foreach ($docBlock->getTags() as $tag) {
                if (($tag instanceof Param || $tag instanceof Return_) && $tag->getType() !== null) {
                    $type = $this->typeToFullyQualified($tag->getType(), $context);
                    if ($type) {
                        $this->namespaces[] = $type;
                    }
                }
            }
        }
    }

    private function typeToFullyQualified(Type $type, Context $context): ?string
    {
        if (!($type instanceof Object_)) {
            return null;
        }

        $typeWithoutBackslashes = trim((string)$type, '\\');
        $resolved = (new TypeResolver())->resolve($typeWithoutBackslashes, $context);
        return $this->fqnIfClassExists($typeWithoutBackslashes) ?:
            $this->fqnIfClassExists((string) $resolved);
    }

    private function fqnIfClassExists(string $type): ?string
    {
        $toCheck = trim($type, '\\');
        if (class_exists($toCheck) || interface_exists($toCheck) || trait_exists($toCheck)) {
            return $toCheck;
        }
        return null;
    }
}
