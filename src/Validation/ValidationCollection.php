<?php
namespace J6s\PhpArch\Validation;

class ValidationCollection extends AbstractValidationCollection
{

    public function addValidator(Validator $validator): self
    {
        $this->validators[] = $validator;
        return $this;
    }
}
