<?php

/**
 * This file is part of the PropelBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Propel\Bundle\PropelBundle\Tests\Fixtures;

use Propel\Generator\Model\Datatype\ColumnType;
use Propel\Runtime\Map\ColumnMap;
use Propel\Runtime\Map\TableMap;

/**
 * Builds map objects the same way for propel/propel and Perpl.
 */
final class MapCompat
{
    public static function column(string $name, TableMap $table, string $phpName, string $type): ColumnMap
    {
        // Perpl takes a ColumnType enum case with the name of the Propel type string
        return new ColumnMap($name, $table, $phpName, enum_exists(ColumnType::class) ? constant(ColumnType::class.'::'.$type) : $type);
    }

    public static function table(string $modelClassName): TableMap
    {
        $table = new TableMap();
        if (method_exists($table, 'setModelClassName')) {
            $table->setModelClassName($modelClassName);
        } else {
            $table->setClassName($modelClassName);
        }

        return $table;
    }
}
