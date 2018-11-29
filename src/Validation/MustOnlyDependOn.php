<?php

namespace J6s\PhpArch\Validation;


use J6s\PhpArch\Utility\NamespaceComperator;

/**
 * Validates that the given namespace only depends on the given destination namespace
 * or itself.
 */
class MustOnlyDependOn implements Validator
{

    /** @var NamespaceComperator */
    private $from;

    /** @var NamespaceComperator */
    private $to;

    /** @var string */
    private $message;

    public function __construct(
        string $from,
        string $to,
        string $message = ':from must only depend on :to but :violatingFrom depends on :violatingTo'
    ) {
        $this->from = new NamespaceComperator($from);
        $this->to = new NamespaceComperator($to);
        $this->message = $message;
    }

    public function isValidBetween(string $from, string $to): bool
    {
        if (!$this->from->matches($from)) {
            return true;
        }

        return $this->to->matches($to) || $this->from->matches($to);
    }

    public function getErrorMessage(string $from, string $to): string
    {
        return str_replace(
            [ ':from', ':to', ':violatingFrom', ':violatingTo' ],
            [ $this->from->getNamespace(), $this->to->getNamespace(), $from, $to ],
            $this->message
        );
    }
}
