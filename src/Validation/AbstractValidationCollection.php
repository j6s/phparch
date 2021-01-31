<?php
namespace J6s\PhpArch\Validation;

abstract class AbstractValidationCollection implements Validator
{
    /** @var Validator[] */
    protected array $validators = [];

    /** @var string[][] */
    private array $errors = [];

    public function __construct(array $validators = [])
    {
        $this->validators = $validators;
    }

    public function isValidBetween(string $from, string $to): bool
    {
        $this->errors = [];

        $valid = true;
        foreach ($this->getValidators() as $validator) {
            if (!$validator->isValidBetween($from, $to)) {
                foreach ($validator->getErrorMessage($from, $to) as $error) {
                    $this->addError($from, $to, $error);
                }
                $valid = false;
            }
        }

        return $valid;
    }

    public function getErrorMessage(string $from, string $to): array
    {
        if (!array_key_exists($from . $to, $this->errors)) {
            return [];
        }

        return $this->errors[$from . $to];
    }


    protected function getValidators(): array
    {
        return $this->validators;
    }

    private function addError(string $from, string $to, string $message): void
    {
        if (!array_key_exists($from . $to, $this->errors)) {
            $this->errors[$from . $to] = [];
        }
        $this->errors[$from . $to][] = $message;
    }
}
