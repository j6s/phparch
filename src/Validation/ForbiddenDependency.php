<?php
namespace J6s\PhpArch\Validation;

use J6s\PhpArch\Utility\NamespaceComperator;

class ForbiddenDependency implements Validator
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
        string $message = 'There must not be any dependency from :fromNamespace to :toNamespace' .
            'but :violatingFrom depends on :violatingTo'
    ) {
        $this->from = new NamespaceComperator($from);
        $this->to = new NamespaceComperator($to);
        $this->message = $message;
    }

    public function isValidBetween(string $from, string $to): bool
    {
        return !$this->from->matches($from) || !$this->to->matches($to);
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
