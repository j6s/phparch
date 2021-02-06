<?php
namespace J6s\PhpArch\Tests;


use J6s\PhpArch\Component\Architecture;
use J6s\PhpArch\PhpArch;
use J6s\PhpArch\Validation\ExplicitlyAllowDependency;
use J6s\PhpArch\Validation\MustBeSelfContained;

/**
 * This class is part of PHParchs own test suite that tests the library
 * and is not meant to be used in projects.
 */
class ArchitectureTest extends TestCase
{

    public function testArchitectureRot(): void
    {
        $architecture = (new Architecture())
            ->components([
                'Component' => 'J6s\\PhpArch\\Component',
                'Validation' => 'J6s\\PhpArch\\Validation',
                'Exceptions' => 'J6s\\PhpArch\\Exception',
                'Parser' => 'J6s\\PhpArch\\Parser',
                'PHP_Core:Exception' => 'Exception'
            ]);

        $architecture->component('Validation')->mustNotDependOn('Component');
        $architecture->component('Exceptions')->mustOnlyDependOn('PHP_Core:Exception');
        $architecture->component('Parser')
            ->mustNotDependOn('Component')
            ->andMustNotDependOn('Validation');

        $utility = new ExplicitlyAllowDependency(
            new MustBeSelfContained('J6s\\PhpArch\\Utility'),
            'J6s\\PhpArch\\Utility',
            'Safe\\'
        );

        (new PhpArch())
            ->fromDirectory(__DIR__ . '/../src')
            ->validate($utility)
            ->validate($architecture)
            ->assertHasNoErrors();
    }

}
