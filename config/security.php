<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('propel.security.user.provider.class', 'Propel\Bundle\PropelBundle\Security\User\PropelUserProvider');

    $container->services()
        ->set('propel.security.user.provider', '%propel.security.user.provider.class%')
            ->abstract()
            ->private();
};
