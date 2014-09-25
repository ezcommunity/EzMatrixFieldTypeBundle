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
