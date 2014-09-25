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
