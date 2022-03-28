<?php

declare(strict_types=1);

namespace J6s\PhpArch\Composer;

class CachedComposerFileParserFactory implements ComposerFileParserFactoryInterface
{
    /** @var array<string, ComposerFileParserInterface>  */
    private array $composerFileParsersCache = [];

    public function create(string $composerFile, string $lockFile = null): ComposerFileParserInterface
    {
        $key = sprintf('%s|%s', $composerFile, $lockFile ?? 'default');

        if (!array_key_exists($key, $this->composerFileParsersCache)) {
            $this->composerFileParsersCache[$key] = new ComposerFileParserCacheDecorator(
                new ComposerFileParser(
                    $composerFile,
                    $lockFile
                )
            );
        }

        return $this->composerFileParsersCache[$key];
    }
}
