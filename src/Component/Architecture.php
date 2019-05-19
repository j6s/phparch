<?php
namespace J6s\PhpArch\Component;

use J6s\PhpArch\Exception\ComponentNotDefinedException;
use J6s\PhpArch\Utility\ComposerFileParser;
use J6s\PhpArch\Validation\ValidationCollection;
use J6s\PhpArch\Utility\ArrayUtility;

class Architecture extends ValidationCollection
{

    /**
     * @var Component[]
     */
    private $components = [];

    /**
     * @var Component|null
     */
    private $currentComponent;

    /**
     * @var Component|null
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
     * Declares components based on the given associative array.
     * The given definitions must be a mapping from the component name to the namespaces
     * defining that component.
     *
     * @example
     * // This
     * $architecture->components([
     *      'Foo' => 'Vendor\\Foo',
     *      'Bar' => [ 'Vendor\\Bar', 'Vendor\\Deep\\Bar' ]
     * ]);
     * // Is the same as this
     * $architecture->component('Foo')->identifiedByNamespace('Vendor\\Foo')
     *      ->component('Bar')->identifierByNamespace('Vendor\\Bar')->identifiedByNamespace('Vendor\\Deep\\Bar')
     *
     * @param string[]|string[][] $definitions
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function components(array $definitions): self
    {
        $currentComponent = $this->currentComponent;
        $lastComponent = $this->lastComponent;

        foreach ($definitions as $name => $identifiedBy) {
            if (!is_array($identifiedBy)) {
                $identifiedBy = [ $identifiedBy ];
            }

            $this->component($name);
            foreach ($identifiedBy as $namespace) {
                $this->identifiedByNamespace($namespace);
            }
        }

        $this->currentComponent = $currentComponent;
        $this->lastComponent = $lastComponent;
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

     * Declares that the currently selected component must not depend on by the component
     * with the given name ignoring interfaces. The declaration of this rule can be made
     * before the second component is defined.
     *
     * @example
     * (new Architecture)
     *      ->component('Logic')->identifiedByNamespace('App\\Logic')
     *      ->mustNotDirectlyDependOn('IO')->identifiedByNamespace('App\\IO')
     *
     * @param string $name
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function mustNotDirectlyDependOn(string $name): self
    {
        $component = $this->ensureComponentExists($name);
        $this->getCurrent()->mustNotDependOn($component, true);
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


    /**
     * Ensures that the current component only depends on namespaces that are declared
     * in the given composer & lock files.
     *
     * If no lock file is passed then the name is automatically generated based on the
     * composer file name.
     *
     * @example
     * $monorepo = (new Architecture)->components([
     *      'PackageOne' => 'Vendor\\Library\\PackageOne',
     *      'PackageTwo' => 'Vendor\\Library\\PackageTwo',
     * ]);
     *
     * $monorepo->component('PackageOne')->mustOnlyDependOnComposerDependencies('Packages/PackageOne/composer.json');
     * $monorepo->component('PackageTwo')->mustOnlyDependOnComposerDependencies('Packages/PackageTwo/composer.json');
     *
     * @param string $composerFile
     * @param string|null $lockFile
     * @param bool $includeDev
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function mustOnlyDependOnComposerDependencies(string $composerFile, ?string $lockFile = null, bool $includeDev = false): self
    {
        $this->getCurrent()->mustOnlyDependOnComposerDependencies(new ComposerFileParser($composerFile, $lockFile), $includeDev);
        return $this;
    }

    /**
     * Adds a new composer based component.
     * In an composer based component the namespaces and dependencies are automatically read from
     * the composer.json file supplied.
     *
     * @example
     * $monorepo = (new Architecture)
     *      ->addComposerBasedComponent('Packages/PackageOne/composer.json')
     *      ->addComposerBasedComponent('Packages/PackageTwo/composer.json');
     *
     * @param string $composerFile
     * @param string|null $lockFile
     * @param string|null $componentName
     * @param bool $includeDev
     * @return $this
     * @throws ComponentNotDefinedException
     */
    public function addComposerBasedComponent(
        string $composerFile,
        ?string $lockFile = null,
        string $componentName = null,
        bool $includeDev = false
    ): self {
        $parser = new ComposerFileParser($composerFile, $lockFile);
        $this->component($componentName ?? $parser->getName());

        foreach ($parser->getNamespaces() as $namespace) {
            $this->getCurrent()->addNamespace($namespace);
        }

        $this->getCurrent()->mustOnlyDependOnComposerDependencies($parser, $includeDev);
        return $this;
    }


    /**
     * Allows adding exceptions to previously declared rules.
     *
     * @example
     * $architecture = new Architecture([
     *      'PackageOne' => 'Vendor\\Namespace\\PackageOne',
     *      'PackageTwo' => 'Vendor\\Namespace\\PackageTwo',
     *      'PackageThree' => 'Vendor\\Namespace\\PackageThree'
     * ]);
     *
     * $architecture->disallowInterdependence([ 'PackageOne', 'PackageTwo', 'PackageThree' ]);
     * $architecture->component('PackageThree')->isAllowedToDependOn('PackageOne');
     *
     *
     * @param string $component
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function isAllowedToDependOn(string $component): self
    {
        $this->getCurrent()->explicitlyAllowDependency($this->ensureComponentExists($component));
        return $this;
    }


    /**
     * Method to declare interdependence:
     * All components in the first array must not depend on any other components in that array.
     *
     * The second key-value array can be used to explicitly allow some dependencies among them.
     *
     * Note: The second key-value array only allows dependencies in this specific context.
     *       If you want to declare a more broadly applicable allowance then the
     *       {@see Architecture::isAllowedToDependOn} is worth looking at.
     * Note: This method assumes that all components have been declared before
     *      (e.g. using the {@see Architecture::components} method).
     *
     * @example
     * $architecture->components([
     *      'Foo' => 'App\\Foo',
     *      'Bar' => 'App\\Bar',
     *      'Baz' => 'App\\Baz',
     * ]);
     *
     * // No dependencies between all 3 components allowed - except for Foo => Baz.
     * $architecture->disallowInterdependence(
     *      [ 'Foo', 'Bar', 'Baz' ],
     *      [ 'Foo' => [ 'Baz' ] ]
     * );
     *
     * @param string[] $components
     * @param array<string, string>[] $allowed
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function disallowInterdependence(array $components, array $allowed = []): self
    {
        ArrayUtility::forEachCombinationInArray($components, function(string $source, string $target) use ($allowed): void {
            if (\in_array($target, $allowed[$source] ?? [], true)) {
                return;
            }
            $this->component($source)->mustNotDependOn($target);
        });

        return $this;
    }

    /**
     * Declares that the current component must not depend on any other previously defined components.
     *
     * Note: This method assumes that all components have been declared before
     *      (e.g. using the {@see Architecture::components} method).
     *
     * @example
     * $architecture->components([
     *      'Core' => 'App\\Core',
     *      'Utilities' => 'App\\Utility',
     *      'Events' => 'App\\Event',
     * ]);
     *
     * $architecture->component('Core')->mustNotDependOnAnyOtherComponent();
     *
     * @return Architecture
     * @throws ComponentNotDefinedException
     */
    public function mustNotDependOnAnyOtherComponent(): self
    {
        $current = $this->getCurrent();
        foreach ($this->components as $component) {
            if ($current !== $component) {
                $current->mustNotDependOn($component);
            }
        }
        return $this;
    }


    private function getCurrent(): Component
    {
        if ($this->currentComponent === null) {
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
