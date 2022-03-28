<?php

declare(strict_types=1);

namespace J6s\PhpArch\Utility;

final class BaseComposerFileParserFactory implements ComposerFileParserFactory
{
    public function create(string $composerFile, string $lockFile = null): ComposerFileParser
    {
        return new ComposerFileParser($composerFile, $lockFile);
    }
}
