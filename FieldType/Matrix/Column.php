<?php
/**
 * Column Object for Matrix FieldType
 * User: joe
 * Date: 12/12/13
 * Time: 8:59 PM
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\MatrixBundle\FieldType\Matrix;

use eZ\Publish\SPI\Persistence\ValueObject;

/**
 * Class Column
 * Column represents a single configured Column for a Matrix
 *
 * @package EzSystems\MatrixBundle\FieldType\Matrix
 */
class Column extends ValueObject
{
    /**
     * Identifier value of the column
     * @var string
     */
    public $id;

    /**
     * User-friendly name for the column
     * @var string
     */
    public $name;

    /**
     * Integer position of the column
     * @var int
     */
    public $num;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Convert the column to an array compatible with the Matrix hash format
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'num' => $this->num
        );
    }
}
