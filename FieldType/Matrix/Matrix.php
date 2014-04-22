<?php
/**
 * Value Object for Matrix FieldType
 * User: joe
 * Date: 12/12/13
 * Time: 8:59 PM
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
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