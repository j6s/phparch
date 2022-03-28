<?php declare(strict_types=1);

namespace J6s\PhpArch\Component;

use J6s\PhpArch\Composer\ComposerFileParserInterface;
use J6s\PhpArch\Validation\AbstractValidationCollection;
use J6s\PhpArch\Validation\AllowInterfaces;
use J6s\PhpArch\Validation\ExplicitlyAllowDependency;
use J6s\PhpArch\Validation\ForbiddenDependency;
use J6s\PhpArch\Validation\MustOnlyDependOn;
use J6s\PhpArch\Validation\MustOnlyDependOnComposerDependencies;
use J6s\PhpArch\Validation\ValidationCollection;
use J6s\PhpArch\Validation\Validator;

class Component extends AbstractValidationCollection
{
    private const MUST_NOT_DEPEND_ON = 'mustNotDepend';
    private const MUST_ONLY_DEPEND_ON = 'mustOnlyDependOn';
    private const MUST_ONLY_DEPEND_ON_COMPOSER_DEPENDENCIES = 'mustOnlyDependOnComposerDependencies';

    private string $name;

    /**
     * The namespaces which identify this component.
     * @var string[]
     */
    private $namespaces = [];

    /** @var array<string, mixed>[] */
    private array $rules = [];

    /** @var Component[] */
    private $explicitlyAllowed = [];

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

    public function mustOnlyDependOnComposerDependencies(
        ComposerFileParserInterface $parser,
        bool $includeDev = false
    ): void {
        $this->rules[] = [
            'type' => self::MUST_ONLY_DEPEND_ON_COMPOSER_DEPENDENCIES,
            'parser' => $parser,
            'includeDev' => $includeDev
        ];
    }

    public function explicitlyAllowDependency(Component $component): void
    {
        $this->explicitlyAllowed[] = $component;
    }

    /** @return string[] */
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

    /** @return Validator[] */
    protected function getValidators(): array
    {
        $collection = new ValidationCollection();
        foreach ($this->rules as $rule) {
            $collection->addValidator($this->ruleToValidator($rule));
        }

        foreach ($this->explicitlyAllowed as $component) {
            foreach ($this->namespaces as $fromNamespace) {
                $collection = new ExplicitlyAllowDependency(
                    $collection,
                    $fromNamespace,
                    $component->getNamespaces()
                );
            }
        }

        return [ $collection ];
    }

    /** @param array<string, mixed> $rule */
    private function ruleToValidator(array $rule): Validator
    {
        $type = $rule['type'] ?? '[UNKNOWN]';

        switch ($type) {
            case self::MUST_NOT_DEPEND_ON:
                $validator = $this->mustNotDependOnValidator($rule['component']);
                break;
            case self::MUST_ONLY_DEPEND_ON:
                $validator = $this->mustOnlyDependOnValidator($rule['component']);
                break;
            case self::MUST_ONLY_DEPEND_ON_COMPOSER_DEPENDENCIES:
                $validator = $this->mustOnlyDependOnComposerDependenciesValidator(
                    $rule['parser'],
                    $rule['includeDev'] ?? false
                );
                break;
            default:
                throw new \InvalidArgumentException('Cannot build rule of type ' . $type);
        }


        if ($rule['allowInterfaces'] ?? false) {
            $validator = new AllowInterfaces($validator);
        }

        return $validator;
    }

    private function mustNotDependOnValidator(Component $component): Validator
    {
        $validators = new ValidationCollection();
        foreach ($this->namespaces as $fromNamespace) {
            foreach ($component->getNamespaces() as $toNamespace) {
                $validators->addValidator(new ForbiddenDependency(
                    $fromNamespace,
                    $toNamespace,
                    $this . ' must not depend on ' . $component .
                    ' but :violatingFrom depends on :violatingTo'
                ));
            }
        }
        return $validators;
    }

    private function mustOnlyDependOnValidator(Component $component): Validator
    {
        $validators = new ValidationCollection();
        foreach ($this->namespaces as $fromNamespace) {
            foreach ($component->getNamespaces() as $toNamespace) {
                $validators->addValidator(new MustOnlyDependOn(
                    $fromNamespace,
                    $toNamespace,
                    $this . ' must only depend on ' . $component .
                    ' but :violatingFrom depends on :violatingTo'
                ));
            }
        }
        return $validators;
    }

    private function mustOnlyDependOnComposerDependenciesValidator(
        ComposerFileParserInterface $parser,
        bool $includeDev
    ): Validator {
        $validators = new ValidationCollection();
        foreach ($this->namespaces as $fromNamespace) {
            $validators->addValidator(new MustOnlyDependOnComposerDependencies($fromNamespace, $parser, $includeDev));
        }
        return $validators;
    }
}
