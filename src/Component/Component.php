<?php

namespace J6s\PhpArch\Component;

use J6s\PhpArch\Validation\AbstractValidationCollection;
use J6s\PhpArch\Validation\AllowInterfaces;
use J6s\PhpArch\Validation\ForbiddenDependency;
use J6s\PhpArch\Validation\MustOnlyDependOn;
use J6s\PhpArch\Validation\ValidationCollection;
use J6s\PhpArch\Validation\Validator;

class Component extends AbstractValidationCollection
{
    private const MUST_NOT_DEPEND_ON = 'mustNotDepend';
    private const MUST_ONLY_DEPEND_ON = 'mustOnlyDependOn';

    /**
     * @var string
     */
    private $name;

    /**
     * The namespaces which identify this component.
     * @var string[]
     */
    private $namespaces = [];

    private $rules = [];

    public function __construct(string $name)
    {
        parent::__construct();
        $this->name = $name;
    }

    public function mustNotDependOn(Component $component, bool $allowInterfaces = false): void
    {
        $this->rules[] = [
            'component' => $component,
            'type' => self::MUST_NOT_DEPEND_ON,
            'allowInterfaces' => $allowInterfaces,
        ];
    }

    public function mustOnlyDependOn(Component $component): void
    {
        $this->rules[] = [
            'component' => $component,
            'type' => self::MUST_ONLY_DEPEND_ON,
            'allowInterfaces' => false
        ];
    }

    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    public function addNamespace(string $namespace): void
    {
        $this->namespaces[] = $namespace;
    }

    public function __toString(): string
    {
        return 'Component(' . $this->name . ')';
    }

    protected function getValidators(): array
    {
        return array_map(
            function (array $rule) {
                return $this->ruleToValidator($rule);
            },
            $this->rules
        );
    }

    private function ruleToValidator(array $rule): Validator
    {
        $validator = new ValidationCollection();

        foreach ($this->getNamespaces() as $fromNamespace) {
            foreach ($rule['component']->getNamespaces() as $toNamespace) {
                $validator->addValidator(
                    $this->createValidator($rule['type'], $fromNamespace, $toNamespace, $rule['component'])
                );
            }
        }

        if ($rule['allowInterfaces']) {
            $validator = new AllowInterfaces($validator);
        }

        return $validator;
    }

    private function createValidator(
        string $type,
        string $fromNamespace,
        string $toNamespace,
        Component $component
    ): Validator {
        switch ($type) {
            case self::MUST_NOT_DEPEND_ON:
                return new ForbiddenDependency(
                    $fromNamespace,
                    $toNamespace,
                    $this . ' must not depend on ' . $component .
                    ' but :violatingFrom depends on :violatingTo'
                );

            case self::MUST_ONLY_DEPEND_ON:
                return new MustOnlyDependOn(
                    $fromNamespace,
                    $toNamespace,
                    $this . ' must only depend on ' . $component .
                    ' but :violatingFrom depends on :violatingTo'
                );

            default:
                throw new \InvalidArgumentException('Cannot build rule of type ' . $type);
        }
    }
}
