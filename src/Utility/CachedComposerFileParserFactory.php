<?php

declare(strict_types=1);

namespace J6s\PhpArch\Utility;

class CachedComposerFileParserFactory implements ComposerFileParserFactory
{
    private array $composerFileParsersCache = [];

    public function create(string $composerFile, string $lockFile = null): ComposerFileParser
    {
        if (false === isset($this->composerFileParsersCache[$composerFile][$lockFile])) {
            $this->composerFileParsersCache[$composerFile][$lockFile] = new CachedComposerFileParser(
                $composerFile,
                $lockFile
            );
        }

        return $this->composerFileParsersCache[$composerFile][$lockFile];
    }
}
