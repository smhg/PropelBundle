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

use Propel\Generator\Model\Datatype\ColumnType;

class Column
{
    private $name;

    private $type;

    public function __construct($name, $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function getColumnType()
    {
        return $this->type;
    }

    public function isText()
    {
        if (!$this->type) {
            return false;
        }

        switch ($this->type) {
            case ColumnType::CHAR:
            case ColumnType::VARCHAR:
            case ColumnType::LONGVARCHAR:
            case ColumnType::BLOB:
            case ColumnType::CLOB:
            case ColumnType::CLOB_EMU:
                return true;
        }

        return false;
    }

    public function getSize()
    {
        return $this->isText() ? 255 : 0;
    }

    public function isNotNull()
    {
        return ('id' === $this->name);
    }
}
