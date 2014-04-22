<?php
/**
 * Test for the Matrix FieldType
 * User: joe
 * Date: 12/12/13
 * Time: 8:59 PM
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\MatrixBundle\Tests;

use EzSystems\MatrixBundle\FieldType\Matrix\Column;
use EzSystems\MatrixBundle\FieldType\Matrix\Row;
use EzSystems\MatrixBundle\FieldType\Matrix\Type as MatrixType;
use EzSystems\MatrixBundle\FieldType\Matrix\Value as MatrixValue;
use EzSystems\MatrixBundle\FieldType\Matrix\Matrix;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\Tests\FieldTypeTest;

class MatrixTest extends FieldTypeTest
{
    /**
     * Returns the field type under test.
     *
     * This method is used by all test cases to retrieve the field type under
     * test. Just create the FieldType instance using mocks from the provided
     * get*Mock() methods and/or custom get*Mock() implementations. You MUST
     * NOT take care for test case wide caching of the field type, just return
     * a new instance from this method!
     *
     * @return FieldType
     */
    protected function createFieldTypeUnderTest()
    {
        $fieldType = new MatrixType();
        $fieldType->setTransformationProcessor( $this->getTransformationProcessorMock() );

        return $fieldType;
    }

    /**
     * Returns the validator configuration schema expected from the field type.
     *
     * @return array
     */
    protected function getValidatorConfigurationSchemaExpectation()
    {
        return array();
    }

    /**
     * Returns the settings schema expected from the field type.
     *
     * @return array
     */
    protected function getSettingsSchemaExpectation()
    {
        return array();
    }

    /**
     * Returns the empty value expected from the field type.
     *
     * @return mixed
     */
    protected function getEmptyValueExpectation()
    {
        return new MatrixValue();
    }

    protected function getColumnConfig()
    {
        return array(
            new Column(
                array(
                    'name' => 'name',
                    'id' => 'name',
                    'num' => 1
                )
            ),
            new Column(
                array(
                    'name' => 'quest',
                    'id' => 'quest',
                    'num' => 2
                )
            ),
            new Column(
                array(
                    'name' => 'colour',
                    'id' => 'colour',
                    'num' => 3
                )
            )
        );
    }

    protected function getColumnConfigHash()
    {
        return array(
            array(
                'id' => 'name',
                'name' => 'name',
                'num' => 1
            ),
            array(
                'id' => 'quest',
                'name' => 'quest',
                'num' => 2
            ),
            array(
                'id' => 'colour',
                'name' => 'colour',
                'num' => 3
            )
        );
    }

    protected function getSingleRow()
    {
        return array(
            new Row(
                array(
                    'name' => 'Lancelot',
                    'quest' => 'Grail',
                    'colour' => 'blue'
                )
            )
        );
    }

    protected function getSingleRowHash()
    {
        return array(
            array(
                'name' => 'Lancelot',
                'quest' => 'Grail',
                'colour' => 'blue'
            )
        );
    }

    protected function getMultipleRows()
    {
        $rows = $this->getSingleRow();
        $rows[] = new Row(
            array(
                'name' => 'Gallahad',
                'quest' => 'Seek Grail',
                'colour' => 'Blue! no, Red! Augh!'
            )
        );

        return $rows;
    }

    /**
     * Data provider for invalid input to acceptValue().
     *
     * Returns an array of data provider sets with 2 arguments: 1. The invalid
     * input to acceptValue(), 2. The expected exception type as a string. For
     * example:
     *
     * <code>
     *  return array(
     *      array(
     *          new \stdClass(),
     *          'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentException',
     *      ),
     *      array(
     *          array(),
     *          'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentException',
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInvalidInputForAcceptValue()
    {
        return array(
            array(
                'My name',
                'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentException',
            ),
            array(
                23,
                'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentException',
            ),
            array(
                array( 'foo' ),
                'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentException',
            ),
        );
    }

    /**
     * Data provider for valid input to acceptValue().
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to acceptValue(), 2. The expected return value from acceptValue().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          __FILE__,
     *          new BinaryFileValue( array(
     *              'path' => __FILE__,
     *              'fileName' => basename( __FILE__ ),
     *              'fileSize' => filesize( __FILE__ ),
     *              'downloadCount' => 0,
     *              'mimeType' => 'text/plain',
     *          ) )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideValidInputForAcceptValue()
    {
        return array(
            array(
                array(),
                new MatrixValue()
            ),
            array(
                $this->getSingleRow(),
                new MatrixValue(
                    $this->getSingleRow(),
                    $this->getColumnConfig()
                )
            )
        );
    }

    /**
     * Provide input for the toHash() method
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to toHash(), 2. The expected return value from toHash().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          new BinaryFileValue( array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ) ),
     *          array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInputForToHash()
    {
        return array(
            array(
                new MatrixValue(
                    $this->getSingleRow(),
                    $this->getColumnConfig()
                ),
                array(
                    'rows' => $this->getSingleRowHash(),
                    'columns' => $this->getColumnConfigHash()
                )
            )
        );
    }

    /**
     * Provide input to fromHash() method
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to fromHash(), 2. The expected return value from fromHash().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ),
     *          new BinaryFileValue( array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ) )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInputForFromHash()
    {
        return array(
            array(
                array(
                    'rows' => $this->getSingleRowHash(),
                    'columns' => $this->getColumnConfigHash()
                ),
                new MatrixValue(
                    $this->getSingleRow(),
                    $this->getColumnConfig()
                )
            )
        );
    }

    public function provideInputForToString()
    {
        return array(
            array(
                new MatrixValue( $this->getSingleRow(), $this->getColumnConfig() ),
                "name\tquest\tcolour\nLancelot\tGrail\tblue"
            ),
            array(
                new MatrixValue( $this->getMultipleRows(), $this->getColumnConfig() ),
                "name\tquest\tcolour\nLancelot\tGrail\tblue\nGallahad\tSeek Grail\tBlue! no, Red! Augh!"
            )
        );
    }

    /**
     * Test output of __toString method.
     * @param MatrixValue $matrix
     * @param $expectedString
     * @dataProvider provideInputForToString
     */
    public function testToString( MatrixValue $matrix, $expectedString )
    {
        $this->assertEquals( $expectedString, (string)$matrix );
    }

    /**
     * Test the name output (expect comma-delimited list of column names)
     */
    public function testGetName()
    {
        $fieldType = $this->getFieldTypeUnderTest();
        $value = new MatrixValue( $this->getSingleRow(), $this->getColumnConfig() );

        $name = $fieldType->getName( $value );

        self::assertEquals( 'name, quest, colour', $name );
    }

}
