<?php declare(strict_types=1);

namespace J6s\PhpArch\Utility;

/**
 * Collection of namespace comperators.
 * Can be used to check whether or not a given namespace is contained
 * in any of the namespace comperators.
 *
 * @see NamespaceComperator
 * @example
 * $component = new NamespaceComperatorCollection([
 *      new NamespaceComperator('App\\Http\\Controllers'),
 *      new NamespaceComperator('App\\ApiV1\\Controllers')
 * ]);
 *
 * $component->containsAny('App\\Http\\Controllers\\MyController');
 * // => true
 *
 * $component->containsAny('App\\ApiV1\\Controllers\\MyController');
 * // => true
 *
 * $component->containsAny('App\\Utility\\PhoneNumberUtility');
 * // => false
 */
class NamespaceComperatorCollection
{

    /** @var NamespaceComperator[]  */
    private array $comperators;

    /** @param string|string[] $namespaces */
    public function __construct($namespaces)
    {
        if (is_string($namespaces)) {
            $namespaces = [ $namespaces ];
        }
        $this->comperators = array_map(
            static function (string $namespace) {
                return new NamespaceComperator($namespace);
            },
            $namespaces
        );
    }

    public function containsAny(string $namespace): bool
    {
        foreach ($this->comperators as $comperator) {
            if ($comperator->contains($namespace)) {
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
