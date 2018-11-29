<?php
namespace J6s\PhpArch\Validation;

class ValidationCollection implements Validator
{

    /** @var Validator[] */
    protected $validators = [];

    /** @var string[][] */
    private $errors = [];

    public function __construct(array $validators = [])
    {
        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }
    }

    public function addValidator(Validator $validator): self
    {
        $this->validators[] = $validator;
        return $this;
    }

    public function isValidBetween(string $from, string $to): bool
    {
        $this->errors = [];

        $valid = true;
        foreach ($this->getValidators() as $validator) {
            if (!$validator->isValidBetween($from, $to)) {
                $this->addError($from, $to, $validator->getErrorMessage($from, $to));
                $valid = false;
            }
        }

        return $valid;
    }

    public function getErrorMessage(string $from, string $to): string
    {
        if (!array_key_exists($from . $to, $this->errors)) {
            return '';
        }

        return implode(', ', $this->errors[$from . $to]);
    }

    protected function getValidators(): array
    {
        return $this->validators;
    }

    private function addError(string $from, string $to, string $message)
    {
        if (!array_key_exists($from . $to, $this->errors)) {
            $this->errors[$from . $to] = [];
        }
        $this->errors[$from . $to][] = $message;
    }
}
