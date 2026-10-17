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

use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;

class ItemQuery
{
    private $map = array(
        'id'            => 'INTEGER',
        'value'         => 'VARCHAR',
        'price'         => 'FLOAT',
        'is_active'     => 'BOOLEAN',
        'enabled'       => 'BOOLEAN_EMU',
        'updated_at'    => 'TIMESTAMP',

        'updated_at'    => 'TIMESTAMP',
        'updated_at'    => 'TIMESTAMP',
        'updated_at'    => 'TIMESTAMP',
    );

    public function getTableMap()
    {
        // Allows to define methods in this class
        // to avoid a lot of mock classes
        return $this;
    }

    public function getPrimaryKeys()
    {
        $cm = MapCompat::column('id', new TableMap(), 'Id', 'INTEGER');

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
            $cm = MapCompat::column($column, new TableMap(), ucfirst($column), $this->map[$column]);
            $cm->setSize($cm->isText() ? 255 : 0);
            $cm->setNotNull('id' === $column);

            return $cm;
        }

        return null;
    }

    /**
     * Method from the TableMap API
     */
    public function getRelations()
    {
        // table maps
        $authorTable = MapCompat::table('\Foo\Author');
        $resellerTable = MapCompat::table('\Foo\Reseller');

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
