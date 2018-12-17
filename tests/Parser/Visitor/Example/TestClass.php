<?php
namespace J6s\PhpArch\Tests\Parser\Visitor\Example;

use Foo\Bar\Baz;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedAnonymousClassDocBlockArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\InstanceCreation\ImportedInstanceCreation;
use J6s\PhpArch\Tests\Parser\Visitor\Example\Traits\ImporetdTrait;
use J6s\PhpArch\Tests\Parser\Visitor\Example\TypeAnnotation\ImportedArgumentAnnotation;
use J6s\PhpArch\Tests\Parser\Visitor\Example\TypeAnnotation\ImportedReturnTypeAnnotation;
use J6s\PhpArch\Tests\Parser\Visitor\Example\StaticMethodCall\ImportedStaticMethodCall;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedDocBlockArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedDocBlockReturn;

class TestClass extends ParentClass implements SomeInterface
{
    use \J6s\PhpArch\Tests\Parser\Visitor\Example\Traits\UsedTrait;
    use ImporetdTrait;

    public function instanceCreation()
    {
        new InstanceCreation\InstanceCreation();
        new ImportedInstanceCreation();
    }

    public function staticMethodCall()
    {
        StaticMethodCall\StaticMethodCall::method();
        ImportedStaticMethodCall::method();
    }

    public function typeAnnotated(TypeAnnotation\ArgumentAnnotation $argument): TypeAnnotation\ReturnTypeAnnotation
    {

    }

    public function typeAnnotatedImported(ImportedArgumentAnnotation $annotation): ImportedReturnTypeAnnotation
    {

    }

    /**
     * @param \J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockArgument $argument
     * @return \J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockReturn
     */
    public function docBlock($argument)
    {

    }


    /**
     * @param ImportedDocBlockArgument $argument
     * @return ImportedDocBlockReturn
     */
    public function importedDocBlock($argument)
    {

    }

    public function anonymousClassFactory()
    {
        return new class {
            /**
             * @param \J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\AnonymousClassDocBlockArgument $argument
             */
            public function docBlock($argument)
            {
            }

            /**
             * @param ImportedAnonymousClassDocBlockArgument $argument
             */
            public function importedDocBlock($argument)
            {
            }
        };
    }
}
