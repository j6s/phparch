<?php

namespace J6s\PhpArch\Utility;

use function Safe\file_get_contents;
use function Safe\json_decode;

class ComposerFileParser
{

    private string $composerFilePath;

    private array $composerFile;

    private string $lockFilePath;

    private array $lockFile;

    private array $lockedPackages;

    public function __construct(string $composerFile, string $lockFile = null)
    {
        if ($lockFile === null) {
            $lockFile = substr($composerFile, 0, -5) . '.lock';
        }

        $this->composerFile = json_decode(file_get_contents($composerFile), true);
        $this->composerFilePath = $composerFile;
        $this->lockFile = json_decode(file_get_contents($lockFile), true);
        $this->lockFilePath = $lockFile;
        $this->lockedPackages = $this->getPackagesFromLockFile();
    }

    /**
     * Returns an array of all namespaces declared by the current composer file.
     *
     * @return string[]
     */
    public function getNamespaces(bool $includeDev = false): array
    {
        return $this->extractNamespaces($this->composerFile, $includeDev);
    }

    /**
     * Returns an array of all required namespaces including deep dependencies (dependencies of dependencies)
     *
     * @return string[]
     */
    public function getDeepRequirementNamespaces(bool $includeDev): array
    {
        $required = $this->getDirectDependencies($includeDev);
        $required = $this->flattenDependencies($required, $includeDev);
        return $this->autoloadableNamespacesForRequirements($required, $includeDev);
    }

    /**
     * Returns an array of directly required package names.
     *
     * @return string[]
     */
    public function getDirectDependencies(bool $includeDev): array
    {
        $required = [];
        foreach (array_keys($this->composerFile['require'] ?? []) as $packageName) {
            $required[] = (string) $packageName;
        }

        if ($includeDev) {
            foreach (array_keys($this->composerFile['require-dev'] ?? []) as $packageName) {
                $required[] = (string) $packageName;
            }
        }

        return $required;
    }

    /**
     * Resolves an array of package names to an array of namespaces declared by those packages.
     *
     * @param string[] $requirements
     * @return string[]
     */
    public function autoloadableNamespacesForRequirements(array $requirements, bool $includeDev): array
    {
        $namespaces = [ [] ];

        foreach ($requirements as $package) {
            $namespaces[] = $this->extractNamespaces($this->lockedPackages[$package], $includeDev);
        }

        return array_merge(...$namespaces);
    }

    public function getComposerFilePath(): string
    {
        return $this->composerFilePath;
    }

    public function getLockFilePath(): string
    {
        return $this->lockFilePath;
    }

    public function getName(): string
    {
        return $this->composerFile['name'];
    }

    private function flattenDependencies(array $topLevelRequirements, bool $includeDev): array
    {
        $required = [];
        $toCheck = $topLevelRequirements;

        while (\count($toCheck) > 0) {
            $packageName = array_pop($toCheck);
            $package = $this->lockedPackages[$packageName] ?? null;
            if ($package === null) {
                continue;
            }

            $required[] = $packageName;

            $deepRequirements = array_keys($package['require'] ?? []);
            if ($includeDev) {
                $deepRequirements = array_merge(
                    $deepRequirements,
                    array_keys($package['require-dev'] ?? [])
                );
            }

            foreach ($deepRequirements as $name) {
                if (!\in_array($name, $required)) {
                    $toCheck[] = $name;
                }
            }
        }

        return $required;
    }

    private function getPackagesFromLockFile(): array
    {
        $lockedPackages = [];

        foreach ($this->lockFile['packages'] ?? [] as $package) {
            $lockedPackages[$package['name']] = $package;
        }

        foreach ($this->lockFile['packages-dev'] ?? [] as $package) {
            $lockedPackages[$package['name']] = $package;
        }

        return $lockedPackages;
    }

    private function extractNamespaces(array $package, bool $includeDev): array
    {
        $namespaces = [];
        foreach (array_keys($package['autoload']['psr-0'] ?? []) as $namespace) {
            $namespaces[] = (string) $namespace;
        }
        foreach (array_keys($package['autoload']['psr-4'] ?? []) as $namespace) {
            $namespaces[] = (string) $namespace;
        }

        if ($includeDev) {
            foreach (array_keys($package['autoload-dev']['psr-0'] ?? []) as $namespace) {
                $namespaces[] = (string) $namespace;
            }
            foreach (array_keys($package['autoload-dev']['psr-4'] ?? []) as $namespace) {
                $namespaces[] = (string) $namespace;
            }
        }

        return $namespaces;
    }
}
