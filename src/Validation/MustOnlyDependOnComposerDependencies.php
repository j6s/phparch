<?php declare(strict_types=1);

namespace J6s\PhpArch\Validation;

use J6s\PhpArch\Composer\ComposerFileParserInterface;
use J6s\PhpArch\Utility\ComposerFileParser;

/**
 * Validates that the given namespace only depends on dependencies defined by the given
 * composer.json file.
 *
 * This can be helpful to prevent accidental dependencies if multiple packages share the
 * same root setup and repository but are independently releasable.
 */
class MustOnlyDependOnComposerDependencies extends MustOnlyDependOn
{

    private ComposerFileParserInterface $parser;

    public function __construct(
        string $from,
        ComposerFileParserInterface $parser,
        bool $includeDev = false,
        string $message = ':from must only depend on dependencies in :composerFile (:lockFile)' .
        ' but :violatingFrom depends on :violatingTo'
    ) {
        $this->parser = $parser;
        parent::__construct($from, $parser->getDeepRequirementNamespaces($includeDev), $message);
    }

    public function isValidBetween(string $from, string $to): bool
    {
        // Blanket assumption: All classes without a namespace (on root level) are
        // PHP internals and thus are always allowed.
        if (strpos($to, '\\') === false) {
            return true;
        }

        return parent::isValidBetween($from, $to);
    }

    public function getErrorMessage(string $from, string $to): array
    {
        return array_map(
            function (string $message): string {
                return str_replace(
                    [ ':composerFile', ':lockFile' ],
                    [ $this->parser->getComposerFilePath(), $this->parser->getLockFilePath() ],
                    $message
                );
            },
            parent::getErrorMessage($from, $to)
        );
    }
}
