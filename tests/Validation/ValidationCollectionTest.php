<?php
namespace J6s\PhpArch\Tests\Validation;


use J6s\PhpArch\Tests\TestCase;
use J6s\PhpArch\Tests\Validation\Mock\TestValidator;
use J6s\PhpArch\Validation\ValidationCollection;
use J6s\PhpArch\Validation\Validator;

class ValidationCollectionTest extends TestCase
{

    public function testIsValidByDefault(): void
    {
        $this->assertTrue((new ValidationCollection())->isValidBetween('Foo', 'Bar'));
    }

    public function testIsValidIfAllValidatorsAreValid(): void
    {
        $validator = new ValidationCollection();
        $validator->addValidator(new TestValidator(true));
        $validator->addValidator(new TestValidator(true));
        $this->assertTrue($validator->isValidBetween('Foo', 'Bar'));
    }

    public function testIsValidIfAtLeastOneValidatorIsInvalid(): void
    {
        $validator = new ValidationCollection();
        $validator->addValidator(new TestValidator(false));
        $validator->addValidator(new TestValidator(true));
        $this->assertFalse($validator->isValidBetween('Foo', 'Bar'));
    }

    public function testReturnsErrorsOfAllValidators()
    {
        $validator = new ValidationCollection();
        $validator->addValidator(new TestValidator(false, 'Foo'));
        $validator->addValidator(new TestValidator(true, 'Bar'));
        $validator->addValidator(new TestValidator(false, 'Baz'));
        $this->assertFalse($validator->isValidBetween('Test', 'PhpArch'));
        $this->assertContains('Foo', $validator->getErrorMessage('Test', 'PhpArch'));
        $this->assertNotContains('Bar', $validator->getErrorMessage('Test', 'PhpArch'));
        $this->assertContains('Baz', $validator->getErrorMessage('Test', 'PhpArch'));
    }

}
