<?php declare(strict_types=1);

namespace J6s\PhpArch\Parser\Visitor;

use J6s\PhpArch\Parser\ParserException;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use PhpParser\Comment\Doc;
use PhpParser\Node;

use function Safe\preg_replace;

class DocBlockTypeAnnotations extends NamespaceCollectingVisitor
{
    private string $lastNamespace;

    /** @var string[] */
    private array $useStatements = [];

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->lastNamespace = $node->name ? $node->name->toString() : '';
            $this->useStatements = [];
        } elseif ($node instanceof Node\Stmt\UseUse) {
            $this->useStatements[$this->extractAlias($node)] = $node->name->toString();
        } elseif ($node instanceof Node\Stmt\ClassMethod) {
            $context = new Context($this->lastNamespace, $this->useStatements);
            if ($node->hasAttribute('comments')) {
                $this->extractDocBlocks((array)$node->getAttribute('comments'), $context);
            }
        }
        return null;
    }

    /** @param Doc[] $docBlocks */
    private function extractDocBlocks(array $docBlocks, Context $context): void
    {
        $factory  = DocBlockFactory::createInstance();

        foreach ($docBlocks as $docBlockString) {
            $docBlockString = $this->transformArraySyntax((string) $docBlockString);
            try {
                $docBlock = $factory->create($docBlockString);
            } catch (\InvalidArgumentException|\RuntimeException $e) {
                throw new ParserException(
                    sprintf("Error parsing dockblock \n\n %s \n\n %s", $docBlockString, $e->getMessage()),
                    $e->getCode(),
                    $e
                );
            }

            foreach ($docBlock->getTags() as $tag) {
                if (($tag instanceof Param || $tag instanceof Return_) && $tag->getType() !== null) {
                    $type = $tag->getType();
                    $typesToResolve = [];

                    if ($type instanceof Object_) {
                        $typesToResolve[] = $type;
                    }
                    // To resolve generic definitions correctly we have to split the type into its original
                    // type and the value part.
                    if ($type instanceof Collection) {
                        $typesToResolve[] = new Object_($type->getFqsen());
                        $typesToResolve[] = $type->getValueType();
                    }
                    foreach ($typesToResolve as $typeToResolve) {
                        $type = $this->typeToFullyQualified($typeToResolve, $context);
                        if ($type) {
                            $this->namespaces[] = $type;
                        }
                    }
                }
            }
        }
    }

    private function transformArraySyntax(string $docBlockString): string
    {
        $docBlock = preg_replace('/array\<(\w+)\>/', '\1[]', $docBlockString);
        if (\is_array($docBlock)) {
            return implode('', $docBlock);
        }
        return $docBlock;
    }

    private function typeToFullyQualified(Type $type, Context $context): ?string
    {
        if (!($type instanceof Object_)) {
            return null;
        }

        // Try to resolve relative to current namespace first
        $resolvedType = (string) (new TypeResolver())->resolve(ltrim((string) $type, '\\'), $context);
        if (class_exists($resolvedType) || interface_exists($resolvedType) || trait_exists($resolvedType)) {
            return ltrim($resolvedType, '\\');
        }

        // Assume absolute reference else
        return ltrim((string) $type, '\\');
    }

    private function extractAlias(Node\Stmt\UseUse $node): string
    {
        if (!method_exists($node, 'getAlias')) {
            // Compatibility mode: nikic/php-parser@3.x
            return (string) $node->alias;
        }

        return $node->getAlias()->toString();
    }

    private function stripLeadingBackslashIfAliasedType(string $namespace, Context $context): string
    {
        $withoutBackslash = ltrim($namespace, '\\');
        [ $firstPart ] = explode('\\', $withoutBackslash);
        if (array_key_exists($firstPart, $context->getNamespaceAliases())) {
            return $withoutBackslash;
        }

        return $namespace;
    }

    private function isImported(string $namespace, Context $context): bool
    {
        [ $firstPart ] = explode($namespace, '\\');
        return array_key_exists((string) $firstPart, $context->getNamespaceAliases());
    }
}
