<?php
/**
 * Row Object for Matrix FieldType
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
 * Class Row
 * Represents a Row in a Matrix
 *
 * @package EzSystems\MatrixBundle\FieldType\Matrix
 */
class Row extends ValueObject
{

    /**
     * Row's index within the row collection.
     *
     * @var int
     */
    public $id;

    /**
     * Associative array of column values
     *
     * @var array
     */
    public $columns;

    public function __construct( array $columns )
    {
        $this->columns = $columns;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode( "\t", $this->columns );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->columns;
    }
}
