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

    /** @var NamespaceComperatorCollection */
    protected $namespace;

    /** @var string */
    private $message;

    /**
     * @param string|string[] $namespace
     * @param string $message
     */
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

    public function getErrorMessage(string $from, string $to): string
    {
        return str_replace(
            [ ':namespace', ':violatingFrom', ':violatingTo' ],
            [ $this->namespace, $from, $to ],
            $this->message
        );
    }
}
