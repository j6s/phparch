<?php declare(strict_types=1);

namespace J6s\PhpArch\Parser\Visitor;

use J6s\PhpArch\Parser\ParserException;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use phpDocumentor\Reflection\DocBlock\Tags\TagWithType;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\AggregatedType;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use PhpParser\Comment\Doc;
use PhpParser\Node;

use function Safe\preg_split;

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
            try {
                $docBlock = $factory->create((string) $docBlockString);
            } catch (\InvalidArgumentException|\RuntimeException $e) {
                throw new ParserException(
                    sprintf("Error parsing dockblock \n\n %s \n\n %s", $docBlockString, $e->getMessage()),
                    $e->getCode(),
                    $e
                );
            }

            $fqsenAlias = [];

            foreach ($docBlock->getTags() as $tag) {
                // Template tags are parsed to local aliases
                if (($tag instanceof Generic) && $tag->getName() === 'template') {
                    [ $source, $target ] = $this->parseTemplateTag((string) $tag->getDescription(), $context);
                    if ($source !== null) {
                        $fqsenAlias[$source] = $target;
                    }
                }

                if (($tag instanceof TagWithType) && $tag->getType() !== null) {
                    $type = $tag->getType();

                    foreach ($this->flattenTypes($type) as $typeToResolve) {
                        if (!($typeToResolve instanceof Object_)) {
                            continue;
                        }
                        $type = $this->typeToFullyQualified((string) $typeToResolve, $context);
                        if ($type && array_key_exists($type, $fqsenAlias)) {
                            $type = $fqsenAlias[$type];
                        }

                        if ($type) {
                            $this->namespaces[] = $type;
                        }
                    }
                }
            }
        }
    }

    /**
     * Returns an array that always contains 2 elements: The source and the target type.
     * If the target type is `null`, then this is an untyped template.
     * If both, source and target are `null`, then the template tag may be invalid.
     *
     * @param string $contents
     * @return (string|null)[]
     */
    private function parseTemplateTag(string $contents, Context $context): array
    {
        $parts = preg_split('/\s+/', $contents);

        // First part is always the name
        $name = $parts[0] ?? null;
        $value = null;

        // Typed template: Has 3 parts of format `TemplateName of BaseType`
        if (\count($parts) === 3 && $parts[1] === 'of') {
            $value = $parts[2];
        }

        return [
            $name ? $this->typeToFullyQualified($name, $context) : null,
            $value ? $this->typeToFullyQualified($value, $context) : null,
        ];
    }

    /**
     * @return Type[]
     */
    private function flattenTypes(Type $type): array
    {
        // Most basic type
        if ($type instanceof Object_) {
            return [ $type ];
        }

        // Array types are comprised of their key & value pairs
        if ($type instanceof Array_) {
            return array_merge(
                $this->flattenTypes($type->getKeyType()),
                $this->flattenTypes($type->getValueType()),
            );
        }

        // To resolve generic definitions correctly we have to split the type into its original
        // type and the value part.
        if ($type instanceof Collection) {
            return array_merge(
                $this->flattenTypes(new Object_($type->getFqsen())),
                $this->flattenTypes($type->getKeyType()),
                $this->flattenTypes($type->getValueType()),
            );
        }

        // Types that consist of multiple parts (e.g. union types: A|B)
        if ($type instanceof AggregatedType) {
            $typeList = [];
            foreach ($type->getIterator() as $innerType) {
                $typeList[] = $this->flattenTypes($innerType);
            }
            return array_merge(...$typeList);
        }

        return [];
    }

    private function typeToFullyQualified(string $type, Context $context): ?string
    {
        if (empty(trim($type))) {
            return null;
        }

        // Try to resolve relative to current namespace first
        $resolvedType = (string) (new TypeResolver())->resolve(ltrim($type, '\\'), $context);
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
}
