<?php

declare(strict_types=1);

namespace J6s\PhpArch\Utility;

class CachedComposerFileParser extends ComposerFileParser
{
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
            $this->namespaces[$key] = parent::getNamespaces($includeDev);
        }

        return $this->namespaces[$key];
    }

    public function getDirectDependencies(bool $includeDev): array
    {
        $key = $includeDev ? self::KEY_WITH_DEV : self::KEY_WITHOUT_DEV;
        if (!array_key_exists($key, $this->directDependencies)) {
            $this->directDependencies[$key] = parent::getDirectDependencies($includeDev);
        }

        return $this->directDependencies[$key];
    }


    public function getDeepRequirementNamespaces(bool $includeDev): array
    {
        $key = $includeDev ? self::KEY_WITH_DEV : self::KEY_WITHOUT_DEV;
        if (!array_key_exists($key, $this->deepRequirementNamespaces)) {
            $this->deepRequirementNamespaces[$key] = parent::getDeepRequirementNamespaces($includeDev);
        }

        return $this->deepRequirementNamespaces[$key];
    }
}
