<?php
/**
 * ColumnCollection for Matrix FieldType
 * User: joe
 * Date: 12/12/13
 * Time: 8:59 PM
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace EzSystems\MatrixBundle\FieldType\Matrix;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;

/**
 * Class ColumnCollection
 * Collection of Column values for Matrix
 *
 * @package EzSystems\MatrixBundle\FieldType\Matrix
 */
class ColumnCollection extends \ArrayObject
{

    /**
     * @param \EzSystems\MatrixBundle\FieldType\Matrix\Column[] $elements
     */
    public function __construct( array $elements = array() )
    {
        // Call parent constructor without $elements because all column elements
        // must be given an id by $this->offsetSet()
        parent::__construct();
        foreach ( $elements as $i => $author )
        {
            $this->offsetSet( $i, $author );
        }
    }

    /**
     * Create a ColumnCollection from an array of field names.
     * The ids will be auto-generated based on the names.
     * @param array $names
     *
     * @return ColumnCollection
     */
    public static function createFromNames( array $names = array() )
    {
        $columns = array();
        $i = 1;

        foreach ( $names as $name )
        {
            $id = strtolower(
                preg_replace(
                    array( '/\s+/', '/[^a-z0-9_]/i' ),
                    array( '_', '' ),
                    $name
                )
            );

            $columns[] = new Column( array( 'id' => $id, 'name' => $name, 'num' => $i ) );
            $i++;
        }

        return new self( $columns );
    }

    /**
     * Adds a new author to the collection
     *
     * @throws InvalidArgumentType When $value is not of type Column
     * @param int $offset
     * @param \EzSystems\MatrixBundle\FieldType\Matrix\Column $value
     */
    public function offsetSet( $offset, $value )
    {
        if ( !$value instanceof Column )
        {
            throw new InvalidArgumentType(
                '$value',
                'EzSystems\\MatrixBundle\\FieldType\\Matrix\\Column',
                $value
            );
        }

        $aColumns = $this->getArrayCopy();
        parent::offsetSet( $offset, $value );
        if ( !isset( $value->id ) || $value->id == -1 )
        {
            if ( !empty( $aColumns ) )
            {
                $value->id = end( $aColumns )->id + 1;
            }
            else
            {
                $value->id = 1;
            }
        }
    }

    /*
     * Returns a comma-separated list of the column names.
     * @return string
     */
    public function getColumnNames()
    {
        return implode( ', ', $this->getArrayCopy() );
    }

    public function __toString()
    {
        return implode( "\t", $this->getArrayCopy() );
    }

    /**
     * Returns an array compatible with the 'columns' portion of the Matrix hash format.
     * @return array
     */
    public function toArray()
    {
        $columns = array();
        foreach ( $this->getArrayCopy() as $column )
        {
            $columns[] = $column->toArray();
        }

        return $columns;
    }
}
