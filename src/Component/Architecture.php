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
     * @var Component
     */
    private $lastComponent;

    /**
     * Adds or selects a component that is identified by the given name.
     * Any subsequent declarations of dependencies reference the component with that name.
     *
     * @param string $name
     * @return Architecture
     */
    public function component(string $name): self
    {
        $this->setCurrent(
            $this->ensureComponentExists($name)
        );
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
     * @example
     * (new Architecture)
     *      ->component('Logic')->identifiedByNamespace('App\\Logic')
     *      ->mustNotDependOn('IO')->identifiedByNamespace('App\\IO')
     *
     * @param string $name
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function mustNotDependOn(string $name): self
    {
        $component = $this->ensureComponentExists($name);
        $this->getCurrent()->mustNotDependOn($component);
        $this->setCurrent($component);
        return $this;
    }

    /**
     * Same as `mustNotDependOn` but refenrences the previous component.
     * This is helpful for 'speaking' chaining.
     *
     * @example
     * (new Architecture)
     *      ->component('Logic')->identifiedByNamespace('App\\Logic')
     *      ->mustNotDependOn('IO')
     *      ->andMustNotDependOn('Controllers')
     *
     *
     * @param string $name
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function andMustNotDependOn(string $name): self
    {
        $this->restoreLast();
        return $this->mustNotDependOn($name);
    }

    /**
     * Declares that the currently selected component must not be depended on by the component
     * with the given name. The declaration of this rule can be made before the second component
     * is defined.
     *
     * @example
     * (new Architecture)
     *      ->component('IO')->identifiedByNamespace('App\\IO')
     *      ->mustNotBeDependedOnBy('Logic')->identifiedBy('App\\Logic');
     *
     * @param string $name
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function mustNotBeDependedOnBy(string $name): self
    {
        $component = $this->ensureComponentExists($name);
        $component->mustNotDependOn($this->getCurrent());
        $this->setCurrent($component);
        return $this;
    }


    /**
     * Same as `mustNotBeDependedOnBy` but refenrences the previous component.
     * This is helpful for 'speaking' chaining.
     *
     * @example
     * @example
     * (new Architecture)
     *      ->component('IO')->identifiedByNamespace('App\\IO')
     *      ->mustNotBeDependedOnBy('Logic')
     *      ->andMustNotBeDependedOnBy('Controllers')
     *
     * @param string $name
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function andMustNotBeDependedOnBy(string $name): self
    {
        $this->restoreLast();
        return $this->mustNotBeDependedOnBy($name);
    }

    /**
     * Declares that the currently selected component must only depend on the component with the
     * given name or itself.
     *
     * @param string $name
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function mustOnlyDependOn(string $name): self
    {
        $component = $this->ensureComponentExists($name);
        $this->getCurrent()->mustOnlyDependOn($component);
        $this->setCurrent($component);
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

    private function restoreLast(): void
    {
        $this->currentComponent = $this->lastComponent;
        $this->lastComponent = null;
    }

    private function setCurrent(Component $component): void
    {
        $this->lastComponent = $this->currentComponent;
        $this->currentComponent = $component;
    }
}
