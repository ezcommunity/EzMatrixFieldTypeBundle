<?php
/**
 * This file is part of the EzMatrixBundle package
 *
 * See README.md file distributed with this source code for further information.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @author For list of contributors see link in composer.json file distributed with this source code.
 */

namespace EzSystems\MatrixBundle\FieldType\Matrix;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;

/**
 * Class RowCollection
 * Collection of Row values for Matrix
 *
 * @package EzSystems\MatrixBundle\FieldType\Matrix
 */
class RowCollection extends \ArrayObject
{

    /**
     * List of all unique column identifiers in the stored rows
     * @var array
     */
    public $columnIds = array();

    /**
     * @param \EzSystems\MatrixBundle\FieldType\Matrix\Row[] $elements
     */
    public function __construct( array $elements = array() )
    {
        // Call parent constructor without $elements because all Row elements
        // must be given an id by $this->offsetSet()
        parent::__construct();
        foreach ( $elements as $i => $row )
        {
            $this->offsetSet( $i, $row );
        }
    }

    /**
     * Adds a new rpw to the collection
     *
     * @throws InvalidArgumentType When $value is not of type Row
     * @param int $offset
     * @param \EzSystems\MatrixBundle\FieldType\Matrix\Row $value
     */
    public function offsetSet( $offset, $value )
    {
        if ( !$value instanceof Row )
        {
            throw new InvalidArgumentType(
                '$value',
                'EzSystems\\MatrixBundle\\FieldType\\Matrix\\Row',
                $value
            );
        }

        $aRows = $this->getArrayCopy();
        parent::offsetSet( $offset, $value );
        $this->columnIds = array_merge( $this->columnIds, array_keys( $value->columns ) );
        if ( !isset( $value->id ) || $value->id == -1 )
        {
            if ( !empty( $aRows ) )
            {
                $value->id = end( $aRows )->id + 1;
            }
            else
            {
                $value->id = 1;
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode( "\n", $this->getArrayCopy() );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $rows = array();
        foreach ( $this->getArrayCopy() as $row )
        {
            $rows[] = $row->toArray();
        }

        return $rows;
    }
}
