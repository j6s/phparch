<?php

declare(strict_types=1);

namespace J6s\PhpArch\Utility;

class CachedComposerFileParserFactory implements ComposerFileParserFactory
{
    /** @var array<string, ComposerFileParser>  */
    private array $composerFileParsersCache = [];

    public function create(string $composerFile, string $lockFile = null): ComposerFileParser
    {
        $key = sprintf('%s|%s', $composerFile, $lockFile ?? 'default');

        if (!array_key_exists($key, $this->composerFileParsersCache)) {
            $this->composerFileParsersCache[$key] = new CachedComposerFileParser(
                $composerFile,
                $lockFile
            );
        }

        return $this->composerFileParsersCache[$key];
    }
}
