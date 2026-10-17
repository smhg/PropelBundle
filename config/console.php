<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Propel\Bundle\PropelBundle\Command;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->private()
            ->bind('$container', service('service_container'))

        ->set('propel_bundle_propel.command.build_command', Command\BuildCommand::class)
            ->tag('console.command', ['command' => 'propel:build'])
        ->set('propel_bundle_propel.command.database_create_command', Command\DatabaseCreateCommand::class)
            ->tag('console.command', ['command' => 'propel:database:create'])
        ->set('propel_bundle_propel.command.database_drop_command', Command\DatabaseDropCommand::class)
            ->tag('console.command', ['command' => 'propel:database:drop'])
        ->set('propel_bundle_propel.command.database_reverse_command', Command\DatabaseReverseCommand::class)
            ->tag('console.command', ['command' => 'propel:database:reverse'])
        ->set('propel_bundle_propel.command.fixtures_dump_command', Command\FixturesDumpCommand::class)
            ->tag('console.command', ['command' => 'propel:fixtures:dump'])
        ->set('propel_bundle_propel.command.fixtures_load_command', Command\FixturesLoadCommand::class)
            ->tag('console.command', ['command' => 'propel:fixtures:load'])
        ->set('propel_bundle_propel.command.form_generate_command', Command\FormGenerateCommand::class)
            ->tag('console.command', ['command' => 'propel:form:generate'])
        ->set('propel_bundle_propel.command.graphviz_generate_command', Command\GraphvizGenerateCommand::class)
            ->tag('console.command', ['command' => 'propel:graphviz:generate'])
        ->set('propel_bundle_propel.command.migration_diff_command', Command\MigrationDiffCommand::class)
            ->tag('console.command', ['command' => 'propel:migration:diff'])
        ->set('propel_bundle_propel.command.migration_down_command', Command\MigrationDownCommand::class)
            ->tag('console.command', ['command' => 'propel:migration:down'])
        ->set('propel_bundle_propel.command.migration_migrate_command', Command\MigrationMigrateCommand::class)
            ->tag('console.command', ['command' => 'propel:migration:migrate'])
        ->set('propel_bundle_propel.command.migration_status_command', Command\MigrationStatusCommand::class)
            ->tag('console.command', ['command' => 'propel:migration:status'])
        ->set('propel_bundle_propel.command.migration_up_command', Command\MigrationUpCommand::class)
            ->tag('console.command', ['command' => 'propel:migration:up'])
        ->set('propel_bundle_propel.command.model_build_command', Command\ModelBuildCommand::class)
            ->tag('console.command', ['command' => 'propel:model:build'])
        ->set('propel_bundle_propel.command.sql_build_command', Command\SqlBuildCommand::class)
            ->tag('console.command', ['command' => 'propel:sql:build'])
        ->set('propel_bundle_propel.command.sql_insert_command', Command\SqlInsertCommand::class)
            ->tag('console.command', ['command' => 'propel:sql:insert'])
        ->set('propel_bundle_propel.command.table_drop_command', Command\TableDropCommand::class)
            ->tag('console.command', ['command' => 'propel:table:drop']);
};
