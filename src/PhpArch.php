<?php
namespace J6s\PhpArch;

use J6s\PhpArch\Parser\Parser;
use J6s\PhpArch\Validation\ValidationCollection;
use J6s\PhpArch\Validation\Validator;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;

class PhpArch
{
    /** @var string[] */
    private $directories = [];

    /** @var ValidationCollection */
    private $validator;

    public function __construct()
    {
        $this->validator = new ValidationCollection();
    }

    /**
     * Executes the validations and returns an array with all errors.
     *
     * @return string[]
     */
    public function errors(): array
    {
        $errors = [];
        $phpParser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $finder = $this->getFinder();

        foreach ($finder->getIterator() as $file) {
            $astParser = new Parser();
            $astParser->process($phpParser->parse($file->getContents()));

            foreach ($astParser->getUsedNamespaces() as $namespace) {
                if (!$this->validator->isValidBetween($astParser->getDeclaredNamespace(), $namespace)) {
                    $errors[] = $this->validator->getErrorMessage($astParser->getDeclaredNamespace(), $namespace);
                }
            }
        }
        return $errors;
    }

    /**
     * Adds a new validation.
     *
     * @param Validator $validator
     * @return PhpArch
     */
    public function validate(Validator $validator): self
    {
        $this->validator->addValidator($validator);
        return $this;
    }

    /**
     * Adds a source directory.
     *
     * @param string $directory
     * @return PhpArch
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
