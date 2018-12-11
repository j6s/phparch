<?php
namespace J6s\PhpArch\Tests\Validation\Mock;


use J6s\PhpArch\Validation\Validator;

class TestValidator implements Validator
{

    private $result;
    private $message;

    public function __construct(bool $result, string $message = '')
    {
        $this->result = $result;
        $this->message = $message;
    }

    public function isValidBetween(string $from, string $to): bool
    {
        return $this->result;
    }

    public function getErrorMessage(string $from, string $to): array
    {
        return [ $this->message ];
    }
}
