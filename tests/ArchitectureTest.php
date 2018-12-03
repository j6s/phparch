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
            ->component('Component')->identifiedByNamespace('J6s\\PhpArch\\Component')
            ->mustNotBeDependedOnBy('Validation')->identifiedByNamespace('J6s\\PhpArch\\Validation')
            ->component('Parser')->mustOnlyDependOn('Vendor:PHParser')->identifiedByNamespace('PhpParser')
            ->component('Exceptions')->mustOnlyDependOn('PHP_Core:Exception')->identifiedByNamespace('Exception');

        (new PhpArch())
            ->fromDirectory(__DIR__ . '/../src')
            ->validate(new MustBeSelfContained('J6s\\PhpArch\\Utility'))
            ->validate($architecture)
            ->assertHasNoErrors();
    }

}
