<?php
namespace J6s\PhpArch;

use J6s\PhpArch\Validation\ValidationCollection;
use J6s\PhpArch\Validation\Validator;
use PhpDA\Parser\Analyzer;
use PhpDA\Parser\AnalyzerFactory;
use PhpDA\Parser\Visitor\Required\DeclaredNamespaceCollector;
use PhpDA\Parser\Visitor\Required\MetaNamespaceCollector;
use PhpDA\Parser\Visitor\Required\UsedNamespaceCollector;
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
        $finder = $this->getFinder();
        $analyzer = $this->getAnalyzer();

        $errors = [];
        foreach ($finder->getIterator() as $file) {
            $analysis = $analyzer->analyze($file);

            foreach ($analysis->getAdts() as $adt) {
                $from = $adt->getDeclaredNamespace()->toString();
                if (!class_exists($from)) {
                    continue;
                }

                foreach ($adt->getCalledNamespaces() as $namespace) {
                    $to = $namespace->toString();
                    if (!class_exists($to)) {
                        continue;
                    }

                    if (!$this->validator->isValidBetween($from, $to)) {
                        $errors[] = $this->validator->getErrorMessage($from, $to);
                    }
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

    public function getAnalyzer(): Analyzer
    {
        $analyzer = (new AnalyzerFactory())->create();
        $analyzer->getNodeTraverser()->bindVisitors([
            DeclaredNamespaceCollector::class,
            MetaNamespaceCollector::class,
            UsedNamespaceCollector::class
        ]);
        return $analyzer;
    }
}
