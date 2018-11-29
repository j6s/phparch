<?php
namespace J6s\PhpArch\Validation;

use J6s\PhpArch\Utility\NamespaceComperator;

/**
 * Validates that the given namespace is self contained - meaning that it may not have
 * any dependency on anything outside of itself.
 */
class MustBeSelfContained implements Validator
{

    /** @var NamespaceComperator */
    protected $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = new NamespaceComperator($namespace);
    }

    public function isValidBetween(string $from, string $to): bool
    {
        if (!$this->namespace->matches($from)) {
            return true;
        }

        return $this->namespace->matches($to);
    }

    public function getErrorMessage(string $from, string $to): string
    {
        return $this->namespace->getNamespace() . ' must be selfcontained, but ' . $from . ' depends on ' . $to;
    }
}
