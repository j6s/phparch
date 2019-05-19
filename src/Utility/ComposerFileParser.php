<?php

namespace J6s\PhpArch\Utility;

use function Safe\file_get_contents;
use function Safe\json_decode;

class ComposerFileParser
{

    /** @var string */
    private $composerFilePath;

    /** @var array */
    private $composerFile;

    /** @var string */
    private $lockFilePath;

    /** @var array */
    private $lockFile;

    /** @var array */
    private $lockedPackages;

    public function getComposerFilePath(): string
    {
        return $this->composerFilePath;
    }

    public function getLockFilePath(): string
    {
        return $this->lockFilePath;
    }

    public function __construct(string $composerFile, string $lockFile = null)
    {
        if ($lockFile === null) {
            $lockFile = substr($composerFile, 0, -4) . '.lock';
        }

        $this->composerFile = json_decode(file_get_contents($composerFile), true);
        $this->composerFilePath = $composerFile;
        $this->lockFile = json_decode(file_get_contents($lockFile), true);
        $this->lockFilePath = $lockFile;
        $this->lockedPackages = $this->getPackagesFromLockFile();
    }

    public function getNamespaces(bool $includeDev = false): array
    {
        $namespaces = array_merge(
            array_keys($this->composerFile['autoload']['psr-0'] ?? []),
            array_keys($this->composerFile['autoload']['psr-4'] ?? [])
        );

        if ($includeDev) {
            $namespaces = array_merge(
                $namespaces,
                array_keys($this->composerFile['autoload-dev']['psr-0'] ?? []),
                array_keys($this->composerFile['autoload-dev']['psr-4'] ?? [])
            );
        }

        return $namespaces;
    }

    public function getDeepRequirementNamespaces(bool $includeDev): array
    {
        $required = $this->getDirectDependencies($includeDev);
        $required = $this->flattenDependencies($required, $includeDev);
        return $this->autoloadableNamespacesForRequirements($required, $includeDev);
    }

    public function getDirectDependencies(bool $includeDev): array
    {
        $required = array_keys($this->composerFile['require'] ?? []);

        if ($includeDev) {
            $required = array_merge($required, array_keys($this->composerFile['require-dev'] ?? []));
        }

        return $required;
    }

    public function autoloadableNamespacesForRequirements(array $requirements, bool $includeDev)
    {
        $namespaces = [];

        foreach ($requirements as $package) {
            $namespaces = array_merge(
                $namespaces,
                array_keys($this->lockedPackages[$package]['autoload']['psr-0'] ?? []),
                array_keys($this->lockedPackages[$package]['autoload']['psr-4'] ?? [])
            );

            if ($includeDev) {
                $namespaces = array_merge(
                    $namespaces,
                    array_keys($this->lockedPackages[$package]['autoload-dev']['psr-0'] ?? []),
                    array_keys($this->lockedPackages[$package]['autoload-dev']['psr-4'] ?? [])
                );
            }
        }

        return $namespaces;
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

        return $lockedPackages;
    }
}
