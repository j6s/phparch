<?php

namespace J6s\PhpArch\Utility;

class NamespaceComperatorCollection
{

    /** @var NamespaceComperator[]  */
    private $comperators;

    /**
     * @param string|string[] $namespaces
     */
    public function __construct($namespaces)
    {
        if (is_string($namespaces)) {
            $namespaces = [ $namespaces ];
        }
        $this->comperators = array_map(
            function (string $namespace) {
                return new NamespaceComperator($namespace);
            },
            $namespaces
        );
    }

    public function matchesAny(string $namespace): bool
    {
        foreach ($this->comperators as $comperator) {
            if ($comperator->matches($namespace)) {
                return true;
            }
        }
        return false;
    }

    public function __toString(): string
    {
        return '[' . implode(', ', $this->comperators) . ']';
    }
}
