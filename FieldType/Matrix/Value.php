<?php
/**
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://ez.no/eZPublish/Licenses/eZ-Trial-and-Test-License-Agreement-eZ-TTL-v2.0 eZ Trial and Test License Agreement Version 2.0
 */

namespace EzSystems\MatrixBundle\FieldType\Matrix;

use eZ\Publish\Core\FieldType\Value as BaseValue;

/**
 * Value for Matrix field type
 *
 * @todo should we declare name, columns and rows as read-only properties?
 */
class Value extends BaseValue
{
    public $rows;
    public $columns;
    public $name;


    /**
     * Construct a new Value object and initialize it
     */
    public function __construct( array $cols, array $rows = array(), $name = '' )
    {
        $this->columns = $cols;
        $this->rows = $rows;
        $this->name = $name;
    }

    /**
     * @see \eZ\Publish\Core\FieldType\Value
     *
     * @bug looses info (col names)
     */
    public function __toString()
    {
        $rows = array();
        foreach( $this->rows as $row )
        {
            $rows[] = Type::implodeAndEscape( '|', $row );
        }
        return Type::implodeAndEscape( '&', $rows );
    }
}
