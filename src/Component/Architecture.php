<?php
namespace J6s\PhpArch\Component;

use J6s\PhpArch\Exception\ComponentNotDefinedException;
use J6s\PhpArch\Validation\ValidationCollection;

class Architecture extends ValidationCollection
{

    /**
     * @var Component[]
     */
    private $components = [];

    /**
     * @var Component
     */
    private $currentComponent;

    /**
     * Adds or selects a component that is identified by the given name.
     * Any subsequent declarations of dependencies reference the component with that name.
     *
     * @param string $name
     * @return Architecture
     */
    public function component(string $name): self
    {
        $this->currentComponent = $this->ensureComponentExists($name);
        return $this;
    }

    /**
     * Defines that the currently selected component is identified by the given namespace.
     * This method can be called multiple times in order to add multiple namespaces to the component.
     *
     * @param string $namespace
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function identifiedByNamespace(string $namespace): self
    {
        $this->getCurrent()->addNamespace($namespace);
        return $this;
    }

    /**
     * Declares that the currently selected component must not depend on by the component
     * with the given name. The declaration of this rule can be made before the second component
     * is defined.
     *
     * @param string $name
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function mustNotDependOn(string $name): self
    {
        $this->getCurrent()->mustNotDependOn($this->ensureComponentExists($name));
        return $this;
    }

    /**
     * Declares that the currently selected component must not be depended on by the component
     * with the given name. The declaration of this rule can be made before the second component
     * is defined.
     *
     * @param string $name
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function mustNotBeDependedOnBy(string $name): self
    {
        $this->ensureComponentExists($name)->mustNotDependOn($this->getCurrent());
        return $this;
    }


    private function getCurrent(): Component
    {
        if (!$this->currentComponent) {
            throw new ComponentNotDefinedException('No current component exists');
        }
        return $this->currentComponent;
    }

    private function ensureComponentExists(string $name): Component
    {
        if (!array_key_exists($name, $this->components)) {
            $this->components[$name] = new Component($name);
            $this->addValidator($this->components[$name]);
        }
        return $this->components[$name];
    }
}
