<?php

declare(strict_types=1);

namespace J6s\PhpArch\Utility;

interface ComposerFileParserFactory
{
    public function create(string $composerFile, string $lockFile = null): ComposerFileParser;
}
