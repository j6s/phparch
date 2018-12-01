<?php
namespace J6s\PhpArch\Tests\Utility;


use J6s\PhpArch\Tests\TestCase;
use J6s\PhpArch\Utility\NamespaceComperatorCollection;

class NamespaceComperatorCollectionTest extends TestCase
{

    /**
     * @param bool $matches
     * @param array $base
     * @param string $compared
     * @dataProvider getNamespaces
     */
    public function testShouldTestIfAnyComperatorContains(bool $matches, array $base, string $compared): void
    {
        $this->assertEquals(
            $matches,
            (new NamespaceComperatorCollection($base))->containsAny($compared)
        );
    }

    public function getNamespaces(): array
    {
        return [
            // Collection with 1 comperator
            [ true, [ 'App\\' ], 'App\\Utility\\Test' ],
            [ false, [ 'App\\' ], 'Lib\\Utility\\Test' ],
            // Collections with multiple comperators
            [ true, [ 'App\\Ns1', 'App\\Ns2' ], 'App\\Ns1\\Test' ],
            [ true, [ 'App\\Ns1', 'App\\Ns2' ], 'App\\Ns2\\Test' ],
            [ false, [ 'App\\Ns1', 'App\\Ns2' ], 'App\\Ns3\\Test' ],
        ];
    }

}
