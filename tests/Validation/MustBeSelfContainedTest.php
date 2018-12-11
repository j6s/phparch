<?php
namespace J6s\PhpArch\Tests\Validation;


use J6s\PhpArch\Tests\TestCase;
use J6s\PhpArch\Validation\MustBeSelfContained;

class MustBeSelfContainedTest extends TestCase
{

    /**
     * @dataProvider getDependencyDefinitions
     * @param string $definition
     * @param string $testFrom
     * @param string $testTo
     * @param bool $isValid
     */
    public function testMarksDependencyAsInvalidIfOutsideOfNamespace(
        string $definition,
        string $testFrom,
        string $testTo,
        bool $isValid
    ): void {
        $this->assertEquals(
            $isValid,
            (new MustBeSelfContained($definition))->isValidBetween($testFrom, $testTo)
        );
    }

    public function getDependencyDefinitions(): array
    {
        return [
            [ 'Foo\\Bar', 'Foo\\Bar\\Component', 'Foo\\Baz\\Component', false ],
            [ 'Foo\\Bar', 'Foo\\Bar\\Component', 'Foo\\Bar\\Another', true ],
            [ 'Foo\\Bar', 'Foo\\Bar\\Component', 'Completely\\Outside', false ],
            [ 'Foo\\Bar', 'Foo\\Bar\\Component', 'Foo\\Baz', false ],
            [ 'Foo\\Bar', 'Completely\\Outside', 'Not\\Related', true ]
        ];
    }

}
