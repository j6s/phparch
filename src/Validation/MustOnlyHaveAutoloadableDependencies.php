<?php
namespace J6s\PhpArch\Validation;

class MustOnlyHaveAutoloadableDependencies implements Validator
{
    /** @var string */
    private $message;

    public function __construct(
        string $message = ':from must only depend on autoloadable dependencies, but :to is not'
    ) {
        $this->message = $message;
    }
    public function isValidBetween(string $from, string $to): bool
    {
        return class_exists($to) || interface_exists($to) || trait_exists($to) || function_exists($to);
    }

    public function getErrorMessage(string $from, string $to): array
    {
        return str_replace([':from', ':to'], [$from, $to], $this->message);
    }
}
