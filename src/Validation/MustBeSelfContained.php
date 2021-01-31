<?php
namespace J6s\PhpArch\Validation;

use J6s\PhpArch\Utility\NamespaceComperator;
use J6s\PhpArch\Utility\NamespaceComperatorCollection;

/**
 * Validates that the given namespace is self contained - meaning that it may not have
 * any dependency on anything outside of itself.
 *
 * Multiple namespaces may be supplied by passing an array to the constructor - in that case
 * those namespaces may only be dependent on each other (in any direction) but not to any
 * namespace that is not in the array.
 */
class MustBeSelfContained implements Validator
{

    protected NamespaceComperatorCollection $namespace;

    private string $message;

    /** @param string|string[] $namespace */
    public function __construct(
        $namespace,
        string $message = ':namespace must be selfcontained, but :violatingFrom depends on :violatingTo'
    ) {
        $this->namespace = new NamespaceComperatorCollection($namespace);
        $this->message = $message;
    }

    public function isValidBetween(string $from, string $to): bool
    {
        if (!$this->namespace->containsAny($from)) {
            return true;
        }

        return $this->namespace->containsAny($to);
    }

    public function getErrorMessage(string $from, string $to): array
    {
        $message = str_replace(
            [ ':namespace', ':violatingFrom', ':violatingTo' ],
            [ $this->namespace, $from, $to ],
            $this->message
        );
        return [ $message ];
    }
}
