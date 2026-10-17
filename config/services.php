<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\ContainerInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->private();

    // Twig\Extension\SyntaxExtension.php is defined in propel.php
    $services->load('Propel\Bundle\PropelBundle\\', '../src/*')
        ->exclude('../src/{ArgumentResolver,Attribute,Console,Propel,Model,Migrations,Helpers,DependencyInjection,Form,Listener,Validator,Twig/Extension/SyntaxExtension.php,DataCollector,AppBundle.php}');

    $services->alias(ContainerInterface::class, 'service_container');
};
