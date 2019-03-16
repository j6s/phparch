<?php
namespace J6s\PhpArch\Tests\Component\Example\Test;



use J6s\PhpArch\Tests\Component\Example\Allowed\AllowedDependency;
use J6s\PhpArch\Tests\Component\Example\Forbidden\ForbiddenDependency;
use J6s\PhpArch\Tests\Component\Example\OutsideDependency;

class TestClass
{

    public function referenceToAllowedDependency()
    {
        new AllowedDependency();
    }

    public function referenceToForbiddenDependency()
    {
        new ForbiddenDependency();
    }

    public function referenceToInsideDependency()
    {
        new InsideDependency();
    }

    public function referenceToOutSideDedenpency()
    {
        new OutsideDependency();
    }

    public function referenceToNonExistingClass()
    {
        new \Non\ExistingClass();
    }

}