<?php declare(strict_types=1);

namespace J6s\PhpArch\Validation;

use J6s\PhpArch\Utility\NamespaceComperator;
use J6s\PhpArch\Utility\NamespaceComperatorCollection;

class ExplicitlyAllowDependency implements Validator
{

    private Validator $validator;

    private NamespaceComperator $from;

    private NamespaceComperatorCollection $to;

    /** @param string|string[] $to */
    public function __construct(Validator $validator, string $from, $to)
    {
        $this->validator = $validator;
        $this->from = new NamespaceComperator($from);
        $this->to = new NamespaceComperatorCollection($to);
    }

    public function isValidBetween(string $from, string $to): bool
    {
        if ($this->isExplicitlyAllowed($from, $to)) {
            return true;
        }

        return $this->validator->isValidBetween($from, $to);
    }

    public function getErrorMessage(string $from, string $to): array
    {
        if ($this->isExplicitlyAllowed($from, $to)) {
            return [];
        }

        return $this->validator->getErrorMessage($from, $to);
    }

    private function isExplicitlyAllowed(string $from, string $to): bool
    {
        return $this->from->contains($from) && $this->to->containsAny($to);
    }
}
