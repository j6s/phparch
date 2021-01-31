<?php declare(strict_types=1);

namespace J6s\PhpArch;

use J6s\PhpArch\Exception\CodeAnalysisException;
use J6s\PhpArch\Parser\Parser;
use J6s\PhpArch\Validation\ValidationCollection;
use J6s\PhpArch\Validation\Validator;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;
use PHPUnit\Framework\Assert;
use PhpParser\Error as PhpParserError;

class PhpArch
{
    /** @var string[] */
    private array $directories = [];

    private ValidationCollection $validator;

    public function __construct()
    {
        $this->validator = new ValidationCollection();
    }

    /**
     * Asserts that the currently configured validations have no errors.
     * This method is intended to be used inside of a PHPUnit test case and
     * will only work if PHPUnit was installed separately from phparch.
     */
    public function assertHasNoErrors(): void
    {
        $errors = $this->errors();
        Assert::assertEmpty($errors, sprintf(
            "%s \n\n\t %d errors occurred while validating architecture\n",
            implode("\n", $errors),
            \count($errors)
        ));
    }

    /**
     * Executes the validations and returns an array with all errors.
     *
     * @return string[]
     */
    public function errors(): array
    {
        $errors = [ [] ];
        $phpParser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $finder = $this->getFinder();

        foreach ($finder->getIterator() as $file) {
            try {
                $abstractSyntaxTree = $phpParser->parse($file->getContents());
            } catch (PhpParserError $e) {
                throw new CodeAnalysisException(
                    sprintf('Error parsing file `%s`', $file->getPathname()),
                    $e->getCode(),
                    $e
                );
            }

            if ($abstractSyntaxTree === null) {
                continue;
            }

            $astParser = new Parser();
            $astParser->process($abstractSyntaxTree);

            foreach ($astParser->getUsedNamespaces() as $namespace) {
                if (!$this->validator->isValidBetween($astParser->getDeclaredNamespace(), $namespace)) {
                    $errors[] = $this->validator->getErrorMessage($astParser->getDeclaredNamespace(), $namespace);
                }
            }
        }

        return array_merge(...$errors);
    }

    /**
     * Adds a new validation.
     */
    public function validate(Validator $validator): self
    {
        $this->validator->addValidator($validator);
        return $this;
    }

    /**
     * Adds a source directory.
     */
    public function fromDirectory(string $directory): self
    {
        $this->directories[] = $directory;
        return $this;
    }

    private function getFinder(): Finder
    {
        return (new Finder())
            ->files()
            ->name('*.php')
            ->in($this->directories)
            ->sortByName();
    }
}
