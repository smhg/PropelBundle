PerplBundle
===========

[![CI](https://github.com/perplorm/perpl-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/perplorm/perpl-bundle/actions/workflows/ci.yml)

Integration of [Perpl](https://perplorm.github.io), the maintained fork of Propel2, in Symfony.

## Requirements

* PHP 8.2+
* Symfony 7.4 LTS or 8.x
* Perpl 2.10+

## Installation

```bash
composer require perplorm/perpl-bundle
```

Register the bundle if Symfony Flex did not do it for you:

```php
// config/bundles.php
return [
    // ...
    Propel\Bundle\PropelBundle\PropelBundle::class => ['all' => true],
];
```

## Configuration

```yaml
# config/packages/propel.yaml
propel:
    database:
        connections:
            default:
                adapter: mysql
                dsn: '%env(DATABASE_DSN)%'
                user: '%env(DATABASE_USER)%'
                password: '%env(DATABASE_PASSWORD)%'
```

Schemas are read from `config/*schema.xml`, from `propel/schema.xml` (see `propel.paths.schemaDir`) and from the `Resources/config/*schema.xml` files of your bundles.

## Features

* Generation of model classes based on XML schemas;
* Insertion of SQL statements;
* Perpl runtime initialization through the bundle configuration;
* Migrations;
* Reverse engineering from an existing database;
* Integration with the Symfony Profiler;
* Loading SQL, YAML and XML fixtures;
* Creating and dropping databases;
* Integration with the Form, Security and Validator components;
* Resolving controller arguments type-hinted with a model class from route parameters.

### Controller arguments

Arguments type-hinted with a model class are looked up by primary key, or by the other route parameters matching a column:

```php
#[Route('/books/{id}')]
public function show(Book $book): Response

#[Route('/books/{slug}')]
public function showBySlug(Book $book): Response
```

Configure the lookup with the `#[MapModel]` attribute:

```php
use Propel\Bundle\PropelBundle\Attribute\MapModel;

#[Route('/authors/{author_id}/books')]
public function books(#[MapModel(mapping: ['author_id' => 'id'], with: 'Book')] Author $author): Response
```

A nullable argument receives `null` when nothing is found, otherwise a 404 is returned.

## License

See [LICENSE](LICENSE).
