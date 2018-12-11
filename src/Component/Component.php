<?php
namespace J6s\PhpArch\Component;

use J6s\PhpArch\Validation\ForbiddenDependency;
use J6s\PhpArch\Validation\MustOnlyDependOn;
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

    /**
     * The component which are the only allowed dependencies for the current component
     * @var Component
     */
    private $mustOnlyDependOn;

    public function __construct(string $name)
    {
        parent::__construct();
        $this->name = $name;
    }

    public function mustNotDependOn(Component $component): void
    {
        $this->forbiddenDependencies[] = $component;
    }

    public function mustOnlyDependOn(Component $component): void
    {
        $this->mustOnlyDependOn = $component;
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

        if ($this->mustOnlyDependOn !== null) {
            $namespacesToOnlyDependOn = \array_merge($this->mustOnlyDependOn->getNamespaces(), $this->namespaces);
            foreach ($this->namespaces as $namespace) {
                $validators[] = new MustOnlyDependOn(
                    $namespace,
                    $namespacesToOnlyDependOn,
                    $this . ' must only depend on ' . $this->mustOnlyDependOn .
                    ' but :violatingFrom depends on :violatingTo'
                );
            }
        }

        foreach (parent::getValidators() as $validator) {
            $validators[] = $validator;
        }

        return $validators;
    }
}
