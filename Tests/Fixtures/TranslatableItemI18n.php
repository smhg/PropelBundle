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

use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Connection\ConnectionInterface;

class TranslatableItemI18n implements ActiveRecordInterface
{
    private $id;

    private $locale;

    private $value;

    private $value2;

    private $item;

    public function __construct($id = null, $locale = null, $value = null)
    {
        $this->id = $id;
        $this->locale = $locale;
        $this->value = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getPrimaryKey()
    {
        return $this->getId();
    }

    public function setPrimaryKey($primaryKey)
    {
        $this->setId($primaryKey);
    }

    public function isPrimaryKeyNull(): bool
    {
        return null === $this->getId();
    }

    public function isModified()
    {
        return false;
    }

    public function isColumnModified($col)
    {
        return false;
    }

    public function isNew()
    {
        return false;
    }

    public function setNew($b)
    {
    }

    public function resetModified()
    {
    }

    public function isDeleted()
    {
        return false;
    }

    public function setDeleted($b)
    {
    }

    public function delete(ConnectionInterface $con = null)
    {
    }

    public function save(ConnectionInterface $con = null)
    {
    }

    public function setLocale($locale)
    {

        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function setItem($item)
    {
        $this->item = $item;
    }

    public function setValue($value)
    {

        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue2($value2)
    {

        $this->value2 = $value2;
    }

    public function getValue2()
    {
        return $this->value2;
    }
}
