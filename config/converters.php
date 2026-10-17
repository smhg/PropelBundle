<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('propel.converter.propel.class', 'Propel\Bundle\PropelBundle\Request\ParamConverter\PropelParamConverter');

    $container->services()
        ->set('propel.converter.propel.orm', '%propel.converter.propel.class%')
            ->tag('request.param_converter', ['converter' => 'propel', 'priority' => 1]);
};
