<?php

declare(strict_types=1);

namespace J6s\PhpArch\Utility;

class CachedComposerFileParser extends ComposerFileParser
{
    private array $deepRequirementNamespaces = [];
    private array $namespaces = [];
    private array $directDependencies = [];

    public function getNamespaces(bool $includeDev = false): array
    {
        if (true === \array_key_exists((int) $includeDev, $this->namespaces)) {
            return $this->namespaces[(int)$includeDev];
        }

        $this->namespaces[(int)$includeDev] = parent::getNamespaces($includeDev);

        return $this->namespaces[(int)$includeDev];
    }

    public function getDirectDependencies(bool $includeDev): array
    {
        if (true === \array_key_exists((int) $includeDev, $this->directDependencies)) {
            return $this->directDependencies[(int)$includeDev];
        }

        $this->directDependencies[(int)$includeDev] = parent::getDirectDependencies($includeDev);

        return $this->directDependencies[(int)$includeDev];
    }


    public function getDeepRequirementNamespaces(bool $includeDev): array
    {
        if (true === \array_key_exists((int) $includeDev, $this->deepRequirementNamespaces)) {
            return $this->deepRequirementNamespaces[(int)$includeDev];
        }

        $this->deepRequirementNamespaces[(int)$includeDev] = parent::getDeepRequirementNamespaces($includeDev);

        return $this->deepRequirementNamespaces[(int)$includeDev];
    }
}
