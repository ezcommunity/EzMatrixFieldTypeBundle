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
 * Class Matrix
 * Value object for the Matrix FieldType
 *
 * @package EzSystems\MatrixBundle\FieldType\Matrix
 */
class Matrix extends ValueObject
{
    public $id;
    public $columns;
    public $rows;
} 