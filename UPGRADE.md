Upgrade to 1.0
==============

PerplBundle 1.0 continues `propel/propel-bundle` and its Symfony 7 forks, such as `skyfox/propel-bundle`. It keeps the bundle class, the PHP namespace, the `propel` configuration and the service IDs, so most applications only need to change `composer.json`.

## Composer

Replace the old bundle package:

```diff
-"skyfox/propel-bundle": "7.0.x-dev",
+"perplorm/perpl-bundle": "^1.0",
```

Keep requiring Propel2 from its `master` branch, or switch to Perpl (see below). PerplBundle requires PHP 8.2 or later and Symfony 7.4 LTS or 8.x. Symfony 8 needs Perpl, because Propel2 does not support it yet.

## Unchanged

* The bundle class `Propel\Bundle\PropelBundle\PropelBundle` and the `Propel\Bundle\PropelBundle\` namespace.
* The `propel` configuration tree.
* The connection setup when the bundle boots, the `propel.logger` service and its `propel` Monolog channel.
* The services `propel.schema_locator`, `propel.logger`, `propel.data_collector`, `propel.twig.extension.syntax`, `form.type_guesser.propel`, `propel.form.type.model`, `propel.dumper.yaml`, `propel.loader.yaml`, `propel.loader.xml` and `propel.security.user.provider`, and their `*.class` parameters.
* The `propel:*` console commands, and the `@Propel` Twig namespace.

## Changed

* Console commands are registered under their class name instead of IDs like `propel_bundle_propel.command.build_command`.
* The bundle no longer autowires all of its classes. Inject its services by the IDs listed above.
* The SensioFrameworkExtraBundle ParamConverter (`propel.converter.propel.orm`) is replaced by a controller argument value resolver, as SensioFrameworkExtraBundle does not support Symfony 7. Mark the arguments to resolve with `#[MapModel]`; arguments with `propel_converter` route options are still resolved too. The resolver ignores all other arguments, so value resolvers of your own keep working.
* `PropelUserProvider::supportsClass()` also accepts subclasses of the user class.

## Deprecated

* `PropelUserProvider::loadUserByUsername()`: use `loadUserByIdentifier()`.
* Passing an array of options to the `UniqueObject` constraint: use named arguments, e.g. `new UniqueObject(fields: ['email'])`. It can now also be used as an attribute.

These keep working in 1.x and will be removed in 2.0.

## Moving to Perpl

Perpl replaces `propel/propel`, so switching is a separate step you can take whenever it suits you:

```diff
-"propel/propel": "dev-master",
+"perplorm/perpl": "^2.10",
```

Then rebuild your models and check the [Perpl documentation](https://perplorm.github.io) for changes that affect your own code. The bundle itself works the same with both.
