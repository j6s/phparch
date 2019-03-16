<?php
namespace J6s\PhpArch\Tests\Validation;


use J6s\PhpArch\Tests\TestCase;
use J6s\PhpArch\Tests\Validation\Mock\ExistingClass;
use J6s\PhpArch\Tests\Validation\Mock\ExistingInterface;
use J6s\PhpArch\Tests\Validation\Mock\ExistingTrait;
use J6s\PhpArch\Validation\MustOnlyHaveAutoloadableDependencies;

class MustOnlyHaveAutoloadableDependenciesTest extends TestCase
{

    /**
     * @dataProvider getDependencyData
     * @param string $from
     * @param string $to
     * @param bool $isValid
     */
    public function testMarksDependencyAsInvalidIfTheDestinationDoesNotExist(string $from, string $to, bool $isValid)
    {
        $this->assertEquals($isValid, (new MustOnlyHaveAutoloadableDependencies())->isValidBetween($from, $to));
    }

    public function getDependencyData(): array
    {
        return [
            [ static::class, ExistingClass::class, true ],
            [ static::class, ExistingInterface::class, true ],
            [ static::class, ExistingTrait::class, true ],
            [ static::class, 'Non\\ExistingClass', false ],
        ];
    }

}