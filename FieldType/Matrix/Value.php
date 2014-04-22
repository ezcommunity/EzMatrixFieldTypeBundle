<?php
/**
 * Value Object for Matrix FieldType
 * User: joe
 * Date: 12/12/13
 * Time: 8:59 PM
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace EzSystems\MatrixBundle\FieldType\Matrix;

use eZ\Publish\Core\FieldType\Value as BaseValue;

/**
 * Class Value
 * Represents the contents of a Matrix field
 *
 * @package EzSystems\MatrixBundle\FieldType\Matrix
 */
class Value extends BaseValue
{

    /**
     * @var RowCollection
     */
    public $rows;

    /**
     * @var ColumnCollection
     */
    public $columns;

    /**
     * @param Row[] $rows
     * @param Column[] $columns
     */
    public function __construct( array $rows = array(), $columns=array() )
    {
        $this->rows = new RowCollection( $rows );

        if ( $columns )
        {
            $this->columns = new ColumnCollection( $columns );
        }
        else
        {
            $this->columns = ColumnCollection::createFromNames( $this->rows->columnIds );
        }
    }

    /**
     * Returns a string representation of the field value.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->columns . "\n" . (string)$this->rows;
    }

}
