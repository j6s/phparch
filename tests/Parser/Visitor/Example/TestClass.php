<?php
namespace J6s\PhpArch\Tests\Parser\Visitor\Example;

use Foo\Bar\Baz;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockArrayItem;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockGenericTypeArrayLong;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockGenericTypeArrayShort;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionGenericChildA;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionGenericChildB;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionGenericChildC;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionGenericChildD;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionGenericWrapperA;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionGenericWrapperB;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\GenericPseudoType;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedAnonymousClassDocBlockArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedGenericArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedGenericClassArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionTypeA;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockUnionTypeB;
use J6s\PhpArch\Tests\Parser\Visitor\Example\InstanceCreation\ImportedInstanceCreation;
use J6s\PhpArch\Tests\Parser\Visitor\Example\Traits\ImporetdTrait;
use J6s\PhpArch\Tests\Parser\Visitor\Example\TypeAnnotation\ImportedArgumentAnnotation;
use J6s\PhpArch\Tests\Parser\Visitor\Example\TypeAnnotation\ImportedReturnTypeAnnotation;
use J6s\PhpArch\Tests\Parser\Visitor\Example\StaticMethodCall\ImportedStaticMethodCall;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedDocBlockArgument;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\ImportedDocBlockReturn;
use J6s\PhpArch\Tests\Parser\Visitor\Example\DocBlock\DocBlockTypedTemplate;

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

    public function simpleFunctionCall()
    {
        \count([]);
    }

    public function referenceToNonExistingClasses()
    {
        new \Foo\Bar\This\Does\Not\Exist();
    }

    public function typeAnnotated(TypeAnnotation\ArgumentAnnotation $argument): TypeAnnotation\ReturnTypeAnnotation
    {

    }

    public function typeAnnotatedImported(ImportedArgumentAnnotation $annotation): ImportedReturnTypeAnnotation
    {

    }

    public function scalarTypeAnnotation(string $foo): int
    {

    }

    public function referenceToItselfInAnnotation(TestClass $argument)
    {

    }

    /**
     * @param TestClass $test
     */
    public function referenceToItselfInDocBlock()
    {

    }

    /**
     * @param string $foo
     * @return int
     */
    public function scalarTypeAnnotationInDocBlock($foo)
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
     * @param \Non\ExistingClass $foo
     * @return \Another\Non\ExistingClass
     */
    public function referenceToNonExistingClassInDocBlock($foo)
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

    /**
     * @param ImportedGenericClassArgument<ImportedGenericArgument> $argument
     * @return ImportedGenericClassArgument<ImportedGenericArgument>
     */
    public function genericsInDocBlock($argument)
    {

    }

    /**
     * @return DocBlockUnionTypeA|DocBlockUnionTypeB
     */
    public function unionTypeInDocBlock()
    {

    }

    /**
     * @return DocBlockUnionGenericWrapperA<DocBlockUnionGenericChildA|DocBlockUnionGenericChildB>|DocBlockUnionGenericWrapperB<DocBlockUnionGenericChildC|DocBlockUnionGenericChildD>
     */
    public function unionAndGenericInDocBlock()
    {

    }

    /**
     * @return DocBlockArrayItem[]
     */
    public function docBlockArray()
    {

    }
    /**
     * @return array<DocBlockGenericTypeArrayShort>
     */
    public function docBlockArrayGenericStyleShort()
    {

    }

    /**
     * @return array<int, DocBlockGenericTypeArrayLong>
     */
    public function docBlockArraydocBlockArrayGenericStyleLong()
    {

    }

    /**
     * @return list<GenericPseudoType>
     */
    public function docBlockGenericPseudoType()
    {

    }

    /**
     * @template UntypedTemplateInDocblock
     * @return UntypedTemplateInDocblock
     */
    public function docBlockUntypedTemplate()
    {

    }

    /**
     * @template TypedTemplateInDocblock of DocBlockTypedTemplate
     * @return TypedTemplateInDocblock
     */
    public function docBlockTypedTemplate()
    {

    }
}
