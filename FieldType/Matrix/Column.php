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
 * Class Column
 * Column represents a single configured Column for a Matrix
 *
 * @package EzSystems\MatrixBundle\FieldType\Matrix
 */
class Column extends ValueObject
{
    /**
     * Identifier value of the column
     * @var string
     */
    public $id;

    /**
     * User-friendly name for the column
     * @var string
     */
    public $name;

    /**
     * Integer position of the column
     * @var int
     */
    public $num;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Convert the column to an array compatible with the Matrix hash format
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'num' => $this->num
        );
    }
}
