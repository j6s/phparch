<?php
namespace J6s\PhpArch\Component;

use J6s\PhpArch\Validation\ForbiddenDependency;
use J6s\PhpArch\Validation\ValidationCollection;
use J6s\PhpArch\Validation\Validator;

class Component extends ValidationCollection
{
    /**
     * @var string
     */
    private $name;

    /**
     * The namespaces which identify this component.
     * @var string[]
     */
    private $namespaces = [];

    /**
     * The components to which the current component may not have any dependencies.
     * @var Component[]
     */
    private $forbiddenDependencies = [];

    public function __construct(string $name)
    {
        parent::__construct();
        $this->name = $name;
    }

    public function mustNotDependOn(Component $component): void
    {
        $this->forbiddenDependencies[] = $component;
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
        $validators = [];
        foreach ($this->forbiddenDependencies as $forbiddenDependency) {
            $message = $this . ' must not depend on ' . $forbiddenDependency .
                ' but :violatingFrom depends on :violatingTo';

            foreach ($this->namespaces as $fromNamespace) {
                foreach ($forbiddenDependency->getNamespaces() as $toNamespace) {
                    $validators[] = new ForbiddenDependency($fromNamespace, $toNamespace, $message);
                }
            }
        }

        foreach (parent::getValidators() as $validator) {
            $validators[] = $validator;
        }

        return $validators;
    }
}
