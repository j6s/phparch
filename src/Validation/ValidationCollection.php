<?php
namespace J6s\PhpArch\Validation;

class ValidationCollection implements Validator
{

    /** @var Validator[] */
    private $validators = [];

    /** @var string[] */
    private $errors = [];

    public function addValidator(Validator $validator): self
    {
        $this->validators[] = $validator;
        return $this;
    }

    public function isValidBetween(string $from, string $to): bool
    {
        $this->errors = [];

        $valid = true;
        foreach ($this->validators as $validator) {
            if (!$validator->isValidBetween($from, $to)) {
                $this->errors[] = $validator->getErrorMessage($from, $to);
                $valid = false;
            }
        }

        return $valid;
    }

    public function getErrorMessage(string $from, string $to): string
    {
        return implode(', ', $this->errors);
    }
}
