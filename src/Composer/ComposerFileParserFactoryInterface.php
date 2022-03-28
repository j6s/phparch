<?php

declare(strict_types=1);

namespace J6s\PhpArch\Composer;

interface ComposerFileParserFactoryInterface
{
    public function create(string $composerFile, string $lockFile = null): ComposerFileParserInterface;
}
