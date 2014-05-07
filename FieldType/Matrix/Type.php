<?php
/**
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://ez.no/eZPublish/Licenses/eZ-Trial-and-Test-License-Agreement-eZ-TTL-v2.0 eZ Trial and Test License Agreement Version 2.0
 *
 * @todo implement validation based on fieldDefinition
 * @todo implement search capabilities
 */

namespace EzSystems\MatrixBundle\FieldType\Matrix;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\FieldType\Value as BaseValue;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;

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
     * Returns the name of the given field value.
     *
     * It will be used to generate content name and url alias if current field is designated
     * to be used in the content name/urlAlias pattern.
     *
     * @param \EzSystems\MatrixBundle\FieldType\Matrix\Value $value
     *
     * @return string
     */
    public function getName( SPIValue $value )
    {
        return $value->name;
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \EzSystems\MatrixBundle\FieldType\Matrix\Value
     */
    public function getEmptyValue()
    {
        return new Value( array() );
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @return array
     */
    protected function getSortInfo( BaseValue $value )
    {
        return false;
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * In the value:
     * - We store rows as indexed php arrays.
     * - And column names as well, for a single reason: PAPI does not allow us to
     *   put col names in serialized format if they are not here but only in fieldDefinition...
     *
     * Pesky little detail: there is nothing in original datatype which prevents
     * the column indexes to be numeric but start from above 0, or not be successive.
     * This poses a problem in tohash/fromhash
     */
    protected function createValueFromInput( $inputValue )
    {
        if ( is_array( $inputValue ) && isset( $inputValue['columns'] ) )
        {
            $inputValue = new Value(
                $inputValue['columns'],
                isset( $inputValue['rows'] ) ? $inputValue['rows'] : array(),
                isset( $inputValue['name'] ) ? $inputValue['name'] : '' );
        }
        /*else if ( !$inputValue instanceof Value )
        {
            throw new InvalidArgumentType(
                '$inputValue',
                'Ez\\MatrixBundle\\FieldType\\Matrix\\Value',
                $inputValue
            );
        }*/

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * Note that this does not include validation after the rules
     * from validators, but only plausibility checks for the general data
     * format.
     *
     * This is an operation method for {@see acceptValue()}.
     *
     * Example implementation:
     * <code>
     *  protected function checkValueStructure( Value $value )
     *  {
     *      if ( !is_array( $value->cookies ) )
     *      {
     *          throw new InvalidArgumentException( "An array of assorted cookies was expected." );
     *      }
     *  }
     * </code>
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \EzSystems\MatrixBundle\FieldType\Matrix\Value $value
     *
     * @return void
     */
    protected function checkValueStructure( BaseValue $value )
    {
        if ( !is_array( $value->columns ) )
        {
            throw new InvalidArgumentType(
                "\$value->columns",
                'array',
                $value->columns
            );
        }
        if ( !is_array( $value->rows ) )
        {
            throw new InvalidArgumentType(
                "\$value->rows",
                'array',
                $value->rows
            );
        }

        // data structure checks - done here to validate copied-over data
        $cols = array();
        foreach( $value->columns as $i => $col )
        {
            if ( !isset( $col['name'] ) || !isset( $col['identifier'] ) )
            {
                throw new InvalidArgumentType(
                    '\$value->columns[' . $i .']',
                    "['name':'...','identifier':'...']",
                    $col
                );
            }
            $cols[$i] = $col['identifier'];
        }
        // consistency checks: same number of cols for each row, same indexes
        foreach( $value->rows as $i => $row )
        {
            if ( array_diff_key( $cols, $row ) )
            {
                /// @todo implement specific extension type?
                throw new InvalidArgumentType(
                    '\$value->rows[' . $i .']',
                    'array(same keys as in col definition)',
                    $row
                );
            }
        }
    }

    /**
     * @param \EzSystems\MatrixBundle\FieldType\Matrix\Value $value
     */
    public function isEmptyValue( SPIValue $value )
    {
        return $value === null || count( $value->rows ) == 0;
    }

    /**
     * Converts an $hash to the Value defined by the field type
     *
     * @param mixed $hash
     *
     * @return \EzSystems\MatrixBundle\FieldType\Matrix\Value $value
     *
     */
    public function fromHash( $hash )
    {
        if ( $hash === null )
        {
            return $this->getEmptyValue();
        }

        return new Value( $hash['columns'], $hash['rows'], $hash['name'] );
    }

    /**
     * Converts a $Value to a hash
     *
     * @param \EzSystems\MatrixBundle\FieldType\Matrix\Value $value
     *
     * @return array
     *
     * q: is this method needed, or will serialization work just fine without?
     */
    public function toHash( SPIValue $value )
    {
        return array (
            'columns' => $value->columns,
            'rows' => $value->rows,
            'name' => $value->name
        );
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
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * If given $inputValue could not be converted or is already an instance of dedicate value object,
     * the method should simply return it.
     *
     * This is an operation method for {@see acceptValue()}.
     *
     * Example implementation:
     * <code>
     *  protected function createValueFromInput( $inputValue )
     *  {
     *      if ( is_array( $inputValue ) )
     *      {
     *          $inputValue = \eZ\Publish\Core\FieldType\CookieJar\Value( $inputValue );
     *      }
     *
     *      return $inputValue;
     *  }
     * </code>
     *
     * @todo: The XSD needs to be defined for this class
     *
     * @param mixed $inputValue
     *
     * @return mixed The potentially converted input value.
     */
    /*protected function createValueFromInput( $inputValue )
    {
        if ( is_string( $inputValue ) ) {
            if ( empty( $inputValue ) ) {
                $inputValue = Value::EMPTY_VALUE;
            }
            $inputValue = new EzXml( $inputValue );
        }

        if ( $inputValue instanceof Input )
        {
            $doc = new DOMDocument;
            $doc->loadXML( $inputValue->getInternalRepresentation() );
            $inputValue = new Value( $doc );
        }

        return $inputValue;
    }*/


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
