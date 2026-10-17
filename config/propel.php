<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('propel.dbal.default_connection', 'default')
        ->set('propel.schema_locator.class', 'Propel\Bundle\PropelBundle\Service\SchemaLocator')
        ->set('propel.data_collector.class', 'Propel\Bundle\PropelBundle\DataCollector\PropelDataCollector')
        ->set('propel.logger.class', 'Propel\Bundle\PropelBundle\Logger\PropelLogger')
        ->set('propel.twig.extension.syntax.class', 'Propel\Bundle\PropelBundle\Twig\Extension\SyntaxExtension')
        ->set('form.type_guesser.propel.class', 'Propel\Bundle\PropelBundle\Form\TypeGuesser')
        ->set('propel.form.type.model.class', 'Propel\Bundle\PropelBundle\Form\Type\ModelType')
        ->set('propel.dumper.yaml.class', 'Propel\Bundle\PropelBundle\DataFixtures\Dumper\YamlDataDumper')
        ->set('propel.loader.yaml.class', 'Propel\Bundle\PropelBundle\DataFixtures\Loader\YamlDataLoader')
        ->set('propel.loader.xml.class', 'Propel\Bundle\PropelBundle\DataFixtures\Loader\XmlDataLoader');

    $container->services()
        ->set('propel.schema_locator', '%propel.schema_locator.class%')
            ->public()
            ->args([service('service_container'), service('file_locator'), '%propel.configuration%'])

        ->set('propel.logger', '%propel.logger.class%')
            ->public()
            ->args([service('logger')->nullOnInvalid(), service('debug.stopwatch')->nullOnInvalid()])
            ->tag('monolog.logger', ['channel' => 'propel'])

        ->set('propel.data_collector', '%propel.data_collector.class%')
            ->private()
            ->args([service('propel.logger')])
            ->tag('data_collector', ['template' => '@Propel/Collector/propel', 'id' => 'propel'])

        ->set('propel.twig.extension.syntax', '%propel.twig.extension.syntax.class%')
            ->tag('twig.extension')

        ->set('form.type_guesser.propel', '%form.type_guesser.propel.class%')
            ->tag('form.type_guesser')

        ->set('propel.form.type.model', '%propel.form.type.model.class%')
            ->tag('form.type', ['alias' => 'model'])

        ->set('propel.dumper.yaml', '%propel.dumper.yaml.class%')
            ->public()
            ->args(['%kernel.project_dir%', '%propel.configuration%'])

        ->set('propel.loader.yaml', '%propel.loader.yaml.class%')
            ->public()
            ->args(['%kernel.project_dir%', '%propel.configuration%', service('faker.generator')->nullOnInvalid()])

        ->set('propel.loader.xml', '%propel.loader.xml.class%')
            ->public()
            ->args(['%kernel.project_dir%', '%propel.configuration%']);
};
