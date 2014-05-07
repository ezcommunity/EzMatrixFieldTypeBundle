<?php
/**
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://ez.no/eZPublish/Licenses/eZ-Trial-and-Test-License-Agreement-eZ-TTL-v2.0 eZ Trial and Test License Agreement Version 2.0
 *
 * @todo implement validation based on fieldDefinition
 * @todo implement search capabilities
 */

namespace EzSystems\MatrixBundle\FieldType\Matrix;

use eZ\Publish\Core\FieldType\FieldType,
    //eZ\Publish\SPI\Persistence\Content\FieldValue,
    eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;

/**
 * The Matrix (ezmatrix) field type.
 */
class Type extends FieldType
{
    /**
     * Return the field type identifier for this field type
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return "ezmatrix";
    }

    /**
     * Returns whether the field type is searchable
     *
     * @return boolean
     */
    public function isSearchable()
    {
        return true;
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @return array
     */
    protected function getSortInfo( $value )
    {
        return false;
    }

    /**
     * In the value:
     * - We store rows as indexed php arrays.
     * - And column names as well, for a single reason: PAPI does not allow us to
     *   put col names in serialized format if they are not here but only in fieldDefinition...
     *
     * Pesky little detail: there is nothing in original datatype which prevents
     * the column indexes to be numeric but start from above 0, or not be successive.
     * This poses a problem in tohash/fromhash
     */
    protected function internalAcceptValue( $inputValue )
    {
        if ( is_array( $inputValue ) && isset( $inputValue['columns'] ) )
        {
            $inputValue = new Value(
                $inputValue['columns'],
                isset( $inputValue['rows'] ) ? $inputValue['rows'] : array(),
                isset( $inputValue['name'] ) ? $inputValue['name'] : '' );
        }
        else if ( !$inputValue instanceof Value )
        {
            throw new InvalidArgumentType(
                '$inputValue',
                'Ez\\MatrixBundle\\FieldType\\Matrix\\Value',
                $inputValue
            );
        }

        // data structure checks - done here to validate copied-over data
        $cols = array();
        foreach( $inputValue->columns as $i => $col )
        {
            if ( !isset( $col['name'] ) || !isset( $col['identifier'] ) )
            {
                throw new InvalidArgumentType(
                    '$inputValue',
                    'Ez\\MatrixBundle\\FieldType\\Matrix\\Value',
                    $inputValue
                );
            }
            $cols[$i] = $col['identifier'];
        }
        // consistency checks: same number of cols for each row, same indexes
        foreach( $inputValue->rows as $i => $row )
        {

            if ( array_diff_key( $cols, $row ) )
            {
                /// @todo implement specific extension type?
                throw new InvalidArgumentType(
                    '$inputValue (columns)',
                    'Ez\\MatrixBundle\\FieldType\\Matrix\\Value',
                    $inputValue
                );
            }
        }

        return $inputValue;
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return eZ\MatrixBundle\FieldType\Matrix\value
     */
    public function getEmptyValue()
    {
        return new Value( array() );
    }

    /**
     * @param eZ\MatrixBundle\FieldType\Matrix\value $value
     */
    public function isEmptyValue( $value )
    {
        return $value === null || count( $value->rows ) == 0;
    }

    public function getName( $value )
    {
        $value = $this->acceptValue( $value );
        return $value->name;
    }

    /**
     * Converts an $hash to the Value defined by the field type
     *
     * @param mixed $hash
     *
     * @return eZ\MatrixBundle\FieldType\Matrix $value
     */
    public function fromHash( $hash )
    {
        if ( $hash === null )
        {
            return $this->getEmptyValue();
        }

        /// @todo shall we validate anything here ???
        return new Value( $hash['columns'], $hash['rows'], $hash['name'] );
    }

    /**
     * Converts a $Value to a hash
     *
     * @param eZ\MatrixBundle\FieldType\Matrix\value $value
     *
     * @return array
     *
     * q: is this method needed, or will serialization work just fine without?
     */
    public function toHash( $value )
    {
        return array (
            'columns' => $value->columns,
            'rows' => $value->rows,
            'name' => $value->name
        );
    }

    /**
     * Helper function
     *
     * Pop quiz: what happens if someone uses '\' as glue?
     */
    public static function implodeAndEscape( $glue, array $values )
    {
        foreach( $values as &$val )
        {
            $val = str_replace( $glue, "\\$glue", str_replace( '\\', '\\\\', $val ) );
        }
        return implode( $glue, $values );
    }
}
