<?php

namespace J6s\PhpArch\Validation;

use J6s\PhpArch\Utility\NamespaceComperator;
use J6s\PhpArch\Utility\NamespaceComperatorCollection;

/**
 * Validates that the given namespace only depends on the given destination namespace
 * or itself.
 *
 * If an array is supplied as the second argument then the first namespace must only depend on
 * any of the second namespaces.
 */
class MustOnlyDependOn implements Validator
{

    private NamespaceComperator $from;

    private NamespaceComperatorCollection $to;

    private string $message;

    /** @param string|string[] $to */
    public function __construct(
        string $from,
        $to,
        string $message = ':from must only depend on :to but :violatingFrom depends on :violatingTo'
    ) {
        $this->from = new NamespaceComperator($from);
        $this->to = new NamespaceComperatorCollection($to);
        $this->message = $message;
    }

    public function isValidBetween(string $from, string $to): bool
    {
        if (!$this->from->contains($from)) {
            return true;
        }

        return $this->to->containsAny($to) || $this->from->contains($to);
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
