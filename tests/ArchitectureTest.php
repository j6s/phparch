<?php
namespace J6s\PhpArch\Tests;


use J6s\PhpArch\Component\Architecture;
use J6s\PhpArch\PhpArch;
use J6s\PhpArch\Validation\MustBeSelfContained;

class ArchitectureTest extends TestCase
{

    public function testArchitectureRot(): void
    {
        $architecture = (new Architecture())
            ->component('Component')                ->identifiedByNamespace('J6s\\PhpArch\\Component')
            ->mustNotBeDependedOnBy('Validation')   ->identifiedByNamespace('J6s\\PhpArch\\Validation')

            ->component('Exceptions')               ->identifiedByNamespace('J6s\\PhpArch\\Exception')
            ->mustOnlyDependOn('PHP_Core:Exception')->identifiedByNamespace('Exception')

            ->component('Parser')                   ->identifiedByNamespace('J6s\\PhpArch\\Parser')
            ->mustNotDependOn('Component')->andMustNotDependOn('Validation');

        (new PhpArch())
            ->fromDirectory(__DIR__ . '/../src')
            ->validate(new MustBeSelfContained('J6s\\PhpArch\\Utility'))
            ->validate($architecture)
            ->assertHasNoErrors();
    }

}
