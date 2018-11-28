<?php
namespace J6s\PhpArch\Utility;

class NamespaceComperator
{

    /** @var string[] */
    private $comparison;

    public function __construct(string $comparison)
    {
        $this->comparison = explode('\\', trim($comparison, '\\'));
    }

    public function getNamespace(): string
    {
        return implode('\\', $this->comparison);
    }

    public function matches(string $namespace): bool
    {
        $parts = explode('\\', trim($namespace, '\\'));
        $end = min(\count($parts), \count($this->comparison));

        for ($i = 0; $i < $end; $i++) {
            if ($this->comparison[$i] !== $parts[$i]) {
                return false;
            }
        }

        return true;
    }
}
