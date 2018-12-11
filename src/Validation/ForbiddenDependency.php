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
        string $message = 'There must not be any dependency from :from to :to' .
        'but :violatingFrom depends on :violatingTo'
    ) {
        $this->from = new NamespaceComperator($from);
        $this->to = new NamespaceComperator($to);
        $this->message = $message;
    }

    public function isValidBetween(string $from, string $to): bool
    {
        return !$this->from->contains($from) || !$this->to->contains($to);
    }

    public function getErrorMessage(string $from, string $to): array
    {
        $message = str_replace(
            [ ':from', ':to', ':violatingFrom', ':violatingTo' ],
            [ $this->from, $this->to, $from, $to ],
            $this->message
        );
        return [ $message ];
    }
}
