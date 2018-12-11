<?php
namespace J6s\PhpArch\Validation;

interface Validator
{

    public function isValidBetween(string $from, string $to): bool;

    public function getErrorMessage(string $from, string $to): array;
}
