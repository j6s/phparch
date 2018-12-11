<?php
namespace J6s\PhpArch\Tests\Validation;


use J6s\PhpArch\Tests\TestCase;
use J6s\PhpArch\Validation\MustOnlyDependOn;

class MustOnlyDependOnTest extends TestCase
{

    /**
     * @dataProvider getDependencyData
     * @param string $definitionFrom
     * @param $definitionTo
     * @param string $testFrom
     * @param string $testTo
     * @param bool $isValid
     */
    public function testMarksNamespaceAsInvalidIfOnlyDependencyIsBroken(
        string $definitionFrom,
        $definitionTo,
        string $testFrom,
        string $testTo,
        bool $isValid
    ): void {
        $this->assertEquals(
            $isValid,
            (new MustOnlyDependOn($definitionFrom, $definitionTo))->isValidBetween($testFrom, $testTo)
        );
    }

    public function getDependencyData(): array
    {
        return [
            [ 'Foo\\Bar', 'Bar\\Baz', 'Foo\\Bar\\Component', 'Foo\\Bar\\InSameNamespace', true ],
            [ 'Foo\\Bar', 'Bar\\Baz', 'Foo\\Bar\\Component', 'Bar\\Baz\\InOtherNamespace', true ],
            [ 'Foo\\Bar', 'Bar\\Baz', 'Foo\\Bar\\Component', 'Completely\\Outside', false ],
            [ 'Foo\\Bar', 'Bar\\Baz', 'Completely\\Outside', 'Foo\\Bar\\InSameNamespace', true ],
            [ 'Foo\\Bar', 'Bar\\Baz', 'Completely\\Outside', 'Bar\\Baz\\InOtherNamespace', true ],
            [ 'Foo\\Bar', 'Bar\\Baz', 'Completely\\Outside', 'Also\\Outside', true ],
            [ 'Foo\\Bar', [ 'Bar', 'Baz' ], 'Foo\\Bar\\Component', 'Bar\\InFirstAllowed', true ],
            [ 'Foo\\Bar', [ 'Bar', 'Baz' ], 'Foo\\Bar\\Component', 'Baz\\InSecondAllowed', true ],
            [ 'Foo\\Bar', [ 'Bar', 'Baz' ], 'Foo\\Bar\\Component', 'Completely\\Outside', false ],
        ];
    }

}
