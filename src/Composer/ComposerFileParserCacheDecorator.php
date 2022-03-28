<?php

declare(strict_types=1);

namespace J6s\PhpArch\Composer;

class ComposerFileParserCacheDecorator implements ComposerFileParserInterface
{
    private ComposerFileParserInterface $decorated;
    public function __construct(ComposerFileParserInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    private const KEY_WITH_DEV = 'with_dev';
    private const KEY_WITHOUT_DEV = 'without_dev';

    /**
     * @var array<string, string[]>
     * @phpstan-var array<self::KEY_*, string[]>
     */
    private array $deepRequirementNamespaces = [];

    /**
     * @var array<string, string[]>
     * @phpstan-var array<self::KEY_*, string[]>
     */
    private array $namespaces = [];

    /**
     * @var array<string, string[]>
     * @phpstan-var array<self::KEY_*, string[]>
     */
    private array $directDependencies = [];

    public function getNamespaces(bool $includeDev = false): array
    {
        $key = $includeDev ? self::KEY_WITH_DEV : self::KEY_WITHOUT_DEV;
        if (!array_key_exists($key, $this->namespaces)) {
            $this->namespaces[$key] = $this->decorated->getNamespaces($includeDev);
        }

        return $this->namespaces[$key];
    }

    public function getDirectDependencies(bool $includeDev): array
    {
        $key = $includeDev ? self::KEY_WITH_DEV : self::KEY_WITHOUT_DEV;
        if (!array_key_exists($key, $this->directDependencies)) {
            $this->directDependencies[$key] = $this->decorated->getDirectDependencies($includeDev);
        }

        return $this->directDependencies[$key];
    }


    public function getDeepRequirementNamespaces(bool $includeDev): array
    {
        $key = $includeDev ? self::KEY_WITH_DEV : self::KEY_WITHOUT_DEV;
        if (!array_key_exists($key, $this->deepRequirementNamespaces)) {
            $this->deepRequirementNamespaces[$key] = $this->decorated->getDeepRequirementNamespaces($includeDev);
        }

        return $this->deepRequirementNamespaces[$key];
    }

    public function autoloadableNamespacesForRequirements(array $requirements, bool $includeDev): array
    {
        return $this->decorated->autoloadableNamespacesForRequirements($requirements, $includeDev);
    }

    public function getComposerFilePath(): string
    {
        return $this->decorated->getComposerFilePath();
    }

    public function getLockFilePath(): string
    {
        return $this->decorated->getLockFilePath();
    }

    public function getName(): string
    {
        return $this->decorated->getName();
    }
}
