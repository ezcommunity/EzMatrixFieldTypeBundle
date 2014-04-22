<?php
/**
 * Matrix FieldType
 * User: joe
 * Date: 12/12/13
 * Time: 8:59 PM
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\MatrixBundle\FieldType\Matrix;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\Value as CoreValue;
use eZ\Publish\SPI\FieldType\Value as SPIValue;

/**
 * Matrix Field Type
 *
 * This type implements the ezmatrix field type.
 *
 * Valid hash format:
 *
 * <code>
 * $hash = array(
 *       'columns' => array(
 *           array(
 *               'id' => 'make',
 *               'name' => 'Make',
 *               'num' => 0
 *           ),
 *           array(
 *               'id' => 'model',
 *               'name' => 'Model',
 *               'num' => 1
 *           ),
 *           array(
 *               'id' => 'year',
 *               'name' => 'Year',
 *               'num' => 2
 *           )
 *       ),
 *       'rows' => array(
 *           array(
 *               'make' => 'Porsche',
 *               'model' => '911',
 *               'year' => '2001'
 *           ),
 *           array(
 *               'make' => 'Lamborghini',
 *               'model' => 'Diablo',
 *               'year' => '2005'
 *           )
 *       )
 *    );
 * </code>
 *
 * @package EzSystems\MatrixBundle\FieldType\Matrix
 */

class Type extends FieldType
{
    /**
     * Returns the field type identifier for this field type
     *
     * This identifier should be globally unique and the implementer of a
     * FieldType must take care for the uniqueness. It is therefore recommended
     * to prefix the field-type identifier by a unique string that identifies
     * the implementer. A good identifier could for example take your companies main
     * domain name as a prefix in reverse order.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return "ezmatrix";
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
     * @param mixed $inputValue
     *
     * @return mixed The potentially converted input value.
     */
    protected function createValueFromInput( $inputValue )
    {
        if ( is_array( $inputValue ) )
        {
            $inputValue = new Value( $inputValue );
        }

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
     * @param \eZ\Publish\Core\FieldType\Value $value
     *
     * @return void
     */
    protected function checkValueStructure( CoreValue $value )
    {
        // TODO: Implement checkValueStructure() method.
    }

    /**
     * Returns a human readable string representation from the given $value
     *
     * It will be used to generate content name and url alias if current field
     * is designated to be used in the content name/urlAlias pattern.
     *
     * The used $value can be assumed to be already accepted by {@link
     * acceptValue()}.
     *
     * @param SPIValue $value
     *
     * @return string
     */
    public function getName( SPIValue $value )
    {
        return $value->columns->getColumnNames();
    }

    /**
     * Returns the empty value for this field type.
     *
     * This value will be used, if no value was provided for a field of this
     * type and no default value was specified in the field definition. It is
     * also used to determine that a user intentionally (or unintentionally) did not
     * set a non-empty value.
     *
     * @return mixed
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Converts an $hash to the Value defined by the field type
     *
     * This is the reverse operation to {@link toHash()}. At least the hash
     * format generated by {@link toHash()} must be converted in reverse.
     * Additional formats might be supported in the rare case that this is
     * necessary. See the class description for more details on a hash format.
     *
     * @param mixed $hash
     *
     * @return mixed
     */
    public function fromHash( $hash )
    {
        $rows = array();
        $columns = array();

        if ( isset( $hash['rows'] ) )
        {
            $rows = array_map(
                function ( $row )
                {
                    return new Row( $row );
                },
                $hash['rows']
            );
        }

        if ( isset( $hash['columns'] ) )
        {
            $columns = array_map(
                function ( $column )
                {
                    return new Column( $column );
                },
                $hash['columns']
            );
        }

        return new Value( $rows, $columns );
    }

    /**
     * Implements the core of {@see acceptValue()}.
     *
     * @param mixed $inputValue
     *
     * @return \eZ\Publish\Core\FieldType\Value The potentially converted and structurally plausible value.
     */
    protected function internalAcceptValue( $inputValue )
    {
        // TODO: Implement internalAcceptValue() method.
        // Get field type settings that define rows and columns
        // Compare against data
    }

    /**
     * Converts the given $value into a plain hash format
     *
     * Converts the given $value into a plain hash format, which can be used to
     * transfer the value through plain text formats, e.g. XML, which do not
     * support complex structures like objects. See the class level doc block
     * for additional information. See the class description for more details on a hash format.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function toHash( SPIValue $value )
    {
        return array(
            'rows' => $value->rows->toArray(),
            'columns' => $value->columns->toArray()
        );
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @param \eZ\Publish\Core\FieldType\Value $value
     *
     * @return mixed
     */
    protected function getSortInfo( CoreValue $value )
    {
        return (string) $value;
    }
}
