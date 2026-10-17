<?php

/**
 * This file is part of the PropelBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Propel\Bundle\PropelBundle\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Propel\Generator\Builder\Om\BuilderType;
use Propel\Generator\Util\QuickBuilder;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * TestCase
 */
class TestCase extends BaseTestCase
{
    public function getContainer()
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.debug'      => false,
            'kernel.project_dir'   => __DIR__ . '/../',
        )));

        $container->setParameter('propel.configuration', array());
        $container->setDefinition('propel', new Definition('Propel\Runtime\Propel'));

        return $container;
    }

    /**
     * load propel database maps
     * @param array $databaseMapsArray
     */
    protected function loadDatabaseMap(array $databaseMapsArray) {
        $serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
        $serviceContainer->initDatabaseMaps(array (
            'default' => $databaseMapsArray
        ));
    }

    /**
     * Builds a schema the same way for propel/propel and Perpl.
     *
     * @param list<string>|null $classTargets class targets like 'tablemap' or 'querystub'
     */
    protected static function buildSchema(string $schema, ?array $classTargets = null): ConnectionInterface
    {
        $builder = new QuickBuilder();
        if (method_exists($builder, 'setSchemaXml')) {
            $builder->setSchemaXml($schema);
            // Perpl takes BuilderType enum cases with these names as values
            $classTargets = null === $classTargets ? null : array_map(BuilderType::from(...), $classTargets);
        } else {
            $builder->setSchema($schema);
        }

        return $builder->build(null, null, null, null, $classTargets);
    }
}
