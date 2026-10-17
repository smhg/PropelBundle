<?php

namespace Propel\Bundle\PropelBundle\Tests\Functional;

use Propel\Bundle\PropelBundle\ArgumentResolver\PropelValueResolver;
use Propel\Bundle\PropelBundle\Security\User\PropelUserProvider;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class KernelTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testServices()
    {
        $container = static::getContainer();

        $this->assertInstanceOf(PropelValueResolver::class, $container->get('propel.value_resolver'));
        $this->assertInstanceOf(PropelUserProvider::class, $container->get('security.user.provider.concrete.users'));
    }

    public function testCommands()
    {
        $application = new Application(static::bootKernel());

        foreach (['propel:build', 'propel:database:create', 'propel:fixtures:load', 'propel:form:generate', 'propel:migration:migrate', 'propel:sql:insert'] as $name) {
            $this->assertTrue($application->has($name), sprintf('Command "%s" is registered.', $name));
        }
    }
}
