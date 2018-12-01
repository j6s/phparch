<?php
namespace J6s\PhpArch\Tests\Parser\Visitor\Example;

use Foo\Bar\Baz;
use Foo\Bar\ImportedInstanceCreation;
use Foo\Bar\ImportedStaticMethodCall;

class TestClass extends \Foo\Bar\ParentClass implements \Foo\Bar\SomeInterface
{
    public function instanceCreation()
    {
        new \Foo\Bar\InstanceCreation();
        new ImportedInstanceCreation();
    }

    public function staticMethodCall()
    {
        \Foo\Bar\StaticMethodCall::method();
        ImportedStaticMethodCall::method();
    }
}
