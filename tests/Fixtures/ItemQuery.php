<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Propel\Bundle\PropelBundle\Tests\Fixtures;

use Propel\Runtime\Map\ColumnMap;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Generator\Model\Datatype\ColumnType;

class ItemQuery
{
    private $map = array(
        'id'            => ColumnType::INTEGER,
        'value'         => ColumnType::VARCHAR,
        'price'         => ColumnType::FLOAT,
        'is_active'     => ColumnType::BOOLEAN,
        'enabled'       => ColumnType::BOOLEAN_EMU,
        'updated_at'    => ColumnType::TIMESTAMP,

        'updated_at'    => ColumnType::TIMESTAMP,
        'updated_at'    => ColumnType::TIMESTAMP,
        'updated_at'    => ColumnType::TIMESTAMP,
    );

    public function getTableMap()
    {
        // Allows to define methods in this class
        // to avoid a lot of mock classes
        return $this;
    }

    public function getPrimaryKeys()
    {
        $cm = new ColumnMap('id', new TableMap(), 'Id', ColumnType::INTEGER);

        return array('id' => $cm);
    }

    /**
     * Method from the TableMap API
     */
    public function hasColumn($column)
    {
        return in_array($column, array_keys($this->map));
    }

    /**
     * Method from the TableMap API
     */
    public function getColumn($column)
    {
        if ($this->hasColumn($column)) {
            return new Column($column, $this->map[$column]);
        }

        return null;
    }

    /**
     * Method from the TableMap API
     */
    public function getRelations()
    {
        // table maps
        $authorTable = new TableMap();
        $authorTable->setModelClassName('\Foo\Author');

        $resellerTable = new TableMap();
        $resellerTable->setModelClassName('\Foo\Reseller');

        $defaultLocalTable = new TableMap('local');
        $defaultForeignTable = new TableMap('foreign');

        // relations
        $mainAuthorRelation = new RelationMap('MainAuthor', $defaultLocalTable, $authorTable);
        $mainAuthorRelation->setType(RelationMap::MANY_TO_ONE);

        $authorRelation = new RelationMap('Author', $defaultLocalTable, $authorTable);
        $authorRelation->setType(RelationMap::ONE_TO_MANY);

        $resellerRelation = new RelationMap('Reseller', $resellerTable, $defaultForeignTable);
        $resellerRelation->setType(RelationMap::MANY_TO_MANY);

        return array(
            $mainAuthorRelation,
            $authorRelation,
            $resellerRelation
        );
    }
}
