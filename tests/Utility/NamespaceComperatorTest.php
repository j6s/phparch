<?php
namespace J6s\PhpArch\Tests\Utility;

use J6s\PhpArch\Tests\TestCase;
use J6s\PhpArch\Utility\NamespaceComperator;

class NamespaceComperatorTest extends TestCase
{

    public function testShouldStripLeadingAndTrailingBackslashes(): void
    {
        $this->assertEquals(
            'Test\\Test',
            (new NamespaceComperator('\\Test\\Test\\'))->__toString()
        );
    }

    /**
     * @param bool $matches
     * @param string $base
     * @param string $compared
     * @dataProvider getNamespaces
     */
    public function testContainsNamespace(bool $matches, string $base, string $compared): void
    {
        $this->assertEquals(
            $matches,
            (new NamespaceComperator($base))->contains($compared)
        );
    }

    public function getNamespaces(): array
    {
        return [
            // direct match
            [ true, 'MyVendor\\MyPackage\\MyComponent', 'MyVendor\\MyPackage\\MyComponent' ],
            // Subclass / namespace
            [ true, 'MyVendor\\MyPackage', 'MyVendor\\MyPackage\\MyComponent' ],
            // Leading & trailing backslashes
            [ true, 'MyVendor\\MyPackage', '\\MyVendor\\MyPackage\\MyComponent' ],
            [ true, 'MyVendor\\MyPackage', 'MyVendor\\MyPackage\\MyComponent\\' ],
            [ true, 'MyVendor\\MyPackage', '\\MyVendor\\MyPackage\\MyComponent\\' ],
            [ true, '\\MyVendor\\MyPackage', '\\MyVendor\\MyPackage\\MyComponent\\' ],
            [ true, 'MyVendor\\MyPackage\\', '\\MyVendor\\MyPackage\\MyComponent\\' ],
            [ true, '\\MyVendor\\MyPackage\\', '\\MyVendor\\MyPackage\\MyComponent\\' ],
            // Whole different namespace altogether
            [ false, 'MyVendor\\MyPackage\\MyComponent', 'MyVendor\\MyPackage\\MyOtherComponent' ],
            // Search is more specific than compared
            [ false, 'MyVendor\\MyPackage\\MyComponent', 'MyVendor\\MyPackage' ]
        ];
    }

}
