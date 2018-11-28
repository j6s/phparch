<?php
namespace J6s\PhpArch\Validation;

use J6s\PhpArch\Utility\NamespaceComperator;

class ForbiddenDependency implements Validator
{

    /** @var NamespaceComperator */
    private $from;

    /** @var NamespaceComperator */
    private $to;

    public function __construct(string $from, string $to)
    {
        $this->from = new NamespaceComperator($from);
        $this->to = new NamespaceComperator($to);
    }

    public function isValidBetween(string $from, string $to): bool
    {
        return !$this->from->matches($from) || !$this->to->matches($to);
    }

    public function getErrorMessage(string $from, string $to): string
    {
        return 'There must not be any dependencies from ' .
            $this->from->getNamespace() .
            ' to ' .
            $this->to->getNamespace() .
            ' but ' .
            $from .
            ' depends on ' .
            $to;
    }
}
