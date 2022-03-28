<?php
namespace J6s\PhpArch\Tests\Composer;


use J6s\PhpArch\Tests\TestCase;
use J6s\PhpArch\Composer\ComposerFileParser;

class ComposerFileParserTest extends TestCase
{

    /** @var ComposerFileParser */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->subject = new ComposerFileParser(__DIR__ . '/Mock/composer.json');
    }

    public function testExtractsName(): void
    {
        $this->assertEquals(
            'j6s/phparch-mock',
            $this->subject->getName()
        );
    }

    public function testExtractsNamespaces(): void
    {
        $this->assertEquals(
            [ 'J6s\\PhpArchMock' ],
            $this->subject->getNamespaces(false)
        );
        $this->assertEquals(
            [ 'J6s\\PhpArchMock', 'J6s\\PhpArchMock\\Tests' ],
            $this->subject->getNamespaces(true)
        );
    }

    public function testShouldExtractDependencies(): void
    {
        $this->assertEquals(
            [ 'thecodingmachine/safe' ],
            $this->subject->getDirectDependencies(false)
        );
        $this->assertEquals(
            [ 'thecodingmachine/safe', 'phpunit/phpunit' ],
            $this->subject->getDirectDependencies(true)
        );
    }

    public function testExtractsNamespacesForPackageName()
    {
        $this->assertContains(
            'Safe\\',
            $this->subject->autoloadableNamespacesForRequirements([ 'thecodingmachine/safe' ], false)
        );
    }

    public function testDeepRequirementNamespacesContainsDepenenciesOfDependencies()
    {
        $namespaces = $this->subject->getDeepRequirementNamespaces(true);

        // phpunit/phpunit depends on doctrine/instantiator
        $this->assertContains('Doctrine\\Instantiator\\', $namespaces);
    }

}
