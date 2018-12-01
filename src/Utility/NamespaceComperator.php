<?php
namespace J6s\PhpArch\Utility;

/**
 * Namespace comperator:
 * Utility class that can be used in order to compare namespaces.
 * The comperator has a base namespace that is compared against and can
 * check whether or not a given other namespace is inside of the base namespace.
 *
 * @example
 * $namespace = new NamespaceComperator('MyVendor\\MyPackage');
 *
 * $namespace->contains('MyVendor\\MyPackage\\MyComponent');
 * // => true
 * $namespace->contains('MyVendor\\MyOtherPackage\\MyComponent');
 * // => false
 */
class NamespaceComperator
{

    /** @var string[] */
    private $comparison;

    public function __construct(string $comparison)
    {
        $this->comparison = explode('\\', trim($comparison, '\\'));
    }

    public function __toString(): string
    {
        return implode('\\', $this->comparison);
    }

    public function contains(string $namespace): bool
    {
        $parts = explode('\\', trim($namespace, '\\'));

        // If the comperator namespace is more specific than the compared namespace
        // then there is no way that the compared namespace can be inside of it.
        if (\count($this->comparison) > \count($parts)) {
            return false;
        }

        $end = min(\count($parts), \count($this->comparison));
        for ($i = 0; $i < $end; $i++) {
            if ($this->comparison[$i] !== $parts[$i]) {
                return false;
            }
        }

        return true;
    }
}
