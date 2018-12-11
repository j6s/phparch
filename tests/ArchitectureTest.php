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

        (new PhpArch())
            ->fromDirectory(__DIR__ . '/../src')
            ->validate(new MustBeSelfContained('J6s\\PhpArch\\Utility'))
            ->validate($architecture)
            ->assertHasNoErrors();
    }

}
