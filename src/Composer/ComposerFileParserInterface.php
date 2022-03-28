<?php

declare(strict_types=1);

namespace J6s\PhpArch\Composer;

interface ComposerFileParserInterface
{
    /**
     * Returns an array of all namespaces declared by the current composer file.
     *
     * @return string[]
     */
    public function getNamespaces(bool $includeDev = false): array;

    /**
     * Returns an array of all required namespaces including deep dependencies (dependencies of dependencies)
     *
     * @return string[]
     */
    public function getDeepRequirementNamespaces(bool $includeDev): array;

    /**
     * Returns an array of directly required package names.
     *
     * @return string[]
     */
    public function getDirectDependencies(bool $includeDev): array;

    /**
     * Resolves an array of package names to an array of namespaces declared by those packages.
     *
     * @param string[] $requirements
     * @return string[]
     */
    public function autoloadableNamespacesForRequirements(array $requirements, bool $includeDev): array;

    public function getComposerFilePath(): string;

    public function getLockFilePath(): string;

    public function getName(): string;
}
