<?php

declare(strict_types=1);

namespace J6s\PhpArch\Composer;

final class ComposerFileParserFactory implements ComposerFileParserFactoryInterface
{
    public function create(string $composerFile, string $lockFile = null): ComposerFileParserInterface
    {
        return new ComposerFileParser($composerFile, $lockFile);
    }
}
