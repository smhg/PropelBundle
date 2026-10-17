<?php

namespace Propel\Bundle\PropelBundle\Tests\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Propel\Bundle\PropelBundle\Validator\Constraints\UniqueObject;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class UniqueObjectTest extends TestCase
{
    public function testFields()
    {
        $this->assertSame(['email'], (new UniqueObject('email'))->fields);
        $this->assertSame(['name', 'email'], (new UniqueObject(['name', 'email']))->fields);
    }

    public function testOptions()
    {
        $constraint = new UniqueObject(fields: 'email', message: 'Taken', errorPath: 'email', groups: ['registration']);

        $this->assertSame('Taken', $constraint->message);
        $this->assertSame('email', $constraint->errorPath);
        $this->assertSame(['registration'], $constraint->groups);
        $this->assertSame(UniqueObject::CLASS_CONSTRAINT, $constraint->getTargets());
    }

    public function testFieldsRequired()
    {
        $this->expectException(ConstraintDefinitionException::class);

        new UniqueObject([]);
    }

    public function testOptionsArrayIsDeprecated()
    {
        $this->expectUserDeprecationMessage('Since perplorm/perpl-bundle 1.0: Passing an array of options to "Propel\Bundle\PropelBundle\Validator\Constraints\UniqueObject" is deprecated, use named arguments instead.');

        $constraint = new UniqueObject(['fields' => ['name', 'email'], 'message' => 'Taken', 'groups' => ['registration']]);

        $this->assertSame(['name', 'email'], $constraint->fields);
        $this->assertSame('Taken', $constraint->message);
        $this->assertSame(['registration'], $constraint->groups);
    }

    public function testOptionsArrayWithUnknownOption()
    {
        $this->expectException(ConstraintDefinitionException::class);

        @new UniqueObject(['fields' => 'email', 'foo' => 'bar']);
    }
}
