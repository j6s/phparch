<?php
namespace J6s\PhpArch\Tests\Validation;


use J6s\PhpArch\Tests\TestCase;
use J6s\PhpArch\Validation\ForbiddenDependency;

class ForbiddenDependencyTest extends TestCase
{

    /**
     * @dataProvider getDependencyDefinitions
     * @param string $definitionFrom
     * @param string $definitionTo
     * @param string $testFrom
     * @param string $testTo
     * @param bool $isValid
     */
    public function testMarksDependencyFromSourceToDestinationAsInvalid(
        string $definitionFrom,
        string $definitionTo,
        string $testFrom,
        string $testTo,
        bool $isValid
    ): void {
        $this->assertEquals(
            $isValid,
            (new ForbiddenDependency($definitionFrom, $definitionTo))->isValidBetween($testFrom, $testTo)
        );
    }

    public function getDependencyDefinitions(): array
    {
        return [
            [ 'Foo\\Bar', 'Foo\\Baz', 'Foo\\Bar\\Component', 'Foo\\Baz\\Component', false ],
            [ 'Foo\\Bar', 'Foo\\Baz', 'Foo\\Bar\\Component', 'Foo\\Bar\\Another', true ],
            [ 'Foo\\Bar', 'Foo\\Baz', 'Foo\\Bar\\Component', 'Completely\\Outside', true ],
            [ 'Foo\\Bar', 'Foo\\Baz', 'Foo\\Bar\\Component', 'Foo\\Baz', false ],
            [ 'Foo\\Bar', 'Foo\\Baz', 'Completely\\Unrelated', 'Foo\\Baz', true ],
        ];
    }

}
