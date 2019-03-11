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

namespace EzSystems\MatrixBundle\Tests;

use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use EzSystems\MatrixBundle\Persistence\Legacy\Content\FieldValue\Converter\Matrix as MatrixConverter;
use PHPUnit\Framework\TestCase;
use DOMDocument;

/**
 * Test for the Matrix storage converter
 */
class MatrixConverterTest extends TestCase
{

    /**
     * @var \EzSystems\MatrixBundle\Persistence\Legacy\Content\FieldValue\Converter\Matrix
     */
    protected $converter;

    protected function setUp()
    {
        parent::setUp();
        $this->converter = new MatrixConverter();
    }

    protected function tearDown()
    {
        unset( $this->converter );
        parent::tearDown();
    }

    protected function getFieldSettingsHash()
    {
        return array(
            'columnList' => array(
                array(
                    'name' => 'Name',
                    'identifier' => 'name',
                    'index' => 0
                ),
                array(
                    'name' => 'Quest',
                    'identifier' => 'quest',
                    'index' => 1
                )
            ),
        );
    }

    protected function getMultiRowMatrixHash()
    {
        return array(
            'rows' => array(
                array(
                    'name' => 'Lancelot',
                    'quest' => 'Grail',
                    'colour' => 'blue'
                ),
                array(
                    'name' => 'Gallahad',
                    'quest' => 'Seek Grail',
                    'colour' => 'Blue! no, Red! Augh!'
                )
            ),
            'columns' => array(
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
            )
        );
    }

    public function testToStorageFieldDefinition()
    {
        $fieldSettingsHash = $this->getFieldSettingsHash();
        $fieldDefinition = new FieldDefinition();
        $fieldDefinition->fieldTypeConstraints->fieldSettings = $fieldSettingsHash;
        $storageFieldValue = new StorageFieldDefinition();

        $this->converter->toStorageFieldDefinition($fieldDefinition, $storageFieldValue);
        $doc = new DOMDocument('1.0', 'utf-8');

        self::assertTrue($doc->loadXML($storageFieldValue->dataText5));
        self::assertEquals($doc->documentElement->tagName, 'ezmatrix');

        $columnsNodes = $doc->getElementsByTagName( 'column-name');

        self::assertEquals( 2, $columnsNodes->length);

        foreach ($doc->getElementsByTagName( 'column-name') as $i => $columnNode) {
            $column = $fieldSettingsHash['columnList'][$i];
            self::assertEquals($column['identifier'], $columnNode->getAttribute('id'));
            self::assertEquals($column['index'], $columnNode->getAttribute('idx') );
            self::assertEquals($column['name'], $columnNode->textContent);
        }
    }

    public function testToFieldDefinition()
    {
        $storageFieldDefinition = new StorageFieldDefinition();
        $storageFieldDefinition->dataText5 = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<ezmatrix>
	<column-name id="name" idx="0">
		Name
	</column-name>
	<column-name id="quest" idx="1">
		Quest
	</column-name>
</ezmatrix>
EOT;

        $doc = new DOMDocument('1.0', 'utf-8');
        self::assertTrue($doc->loadXML($storageFieldDefinition->dataText5));

        $fieldDefinition = new FieldDefinition();
        $this->converter->toFieldDefinition($storageFieldDefinition, $fieldDefinition);

        self::assertArrayHasKey('columnList', $fieldDefinition->fieldTypeConstraints->fieldSettings);
        self::assertInternalType('array', $fieldDefinition->fieldTypeConstraints->fieldSettings['columnList']);

        $matrixHash = $fieldDefinition->fieldTypeConstraints->fieldSettings;

        $columnNodes = $doc->getElementsByTagName( 'column-name');

        $colSize = count($matrixHash['columnList']);

        self::assertEquals($colSize, $columnNodes->length);

        foreach ($matrixHash['columnList'] as $i => $column) {
            $columnNode = $columnNodes->item($i);

            self::assertEquals(
                $columnNode->textContent,
                $column['name']
            );

            self::assertEquals(
                $columnNode->attributes->getNamedItem('id')->nodeValue,
                $column['identifier']
            );

            self::assertEquals(
                $columnNode->attributes->getNamedItem('idx')->nodeValue,
                $column['index']
            );
        }
    }

    public function testToStorageValue()
    {
        $matrixHash = $this->getMultiRowMatrixHash();
        $value = new FieldValue;
        $value->data = $matrixHash;
        $storageFieldValue = new StorageFieldValue;

        $this->converter->toStorageValue( $value, $storageFieldValue );
        $doc = new DOMDocument( '1.0', 'utf-8' );
        self::assertTrue( $doc->loadXML( $storageFieldValue->dataText ) );

        self::assertEquals( $doc->documentElement->tagName, 'ezmatrix' );

        $columnsNodes = $doc->getElementsByTagName( 'columns' );

        $colSize = count( $matrixHash['columns'] );

        self::assertEquals( 1, $columnsNodes->length );
        self::assertEquals(
            $colSize,
            (int)$columnsNodes->item( 0 )->attributes->getNamedItem( 'number' )->nodeValue
        );

        foreach ( $doc->getElementsByTagName( 'column' ) as $i => $columnNode )
        {
            $column = $matrixHash['columns'][$i];
            self::assertEquals( $column['id'], $columnNode->getAttribute( 'id' ) );
            self::assertEquals( $column['num'], $columnNode->getAttribute( 'num' ) );
            self::assertEquals( $column['name'], $columnNode->textContent );
        }

        $rowsNodes = $doc->getElementsByTagName( 'rows' );

        $rowSize = count( $matrixHash['rows'] );

        self::assertEquals( 1, $rowsNodes->length );
        self::assertEquals(
            $rowSize,
            (int)$rowsNodes->item( 0 )->attributes->getNamedItem( 'number' )->nodeValue
        );

        foreach ( $doc->getElementsByTagName( 'c' ) as $i => $cNode )
        {
            //Select the correct row, values only
            $row = array_values( $matrixHash['rows'][floor( $i / $colSize )] );
            self::assertEquals( $row[ $i % $colSize], $cNode->textContent );
        }
    }

    public function testToFieldValue()
    {
        $storageFieldValue = new StorageFieldValue;
        $storageFieldValue->dataText = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<ezmatrix>
	<columns number="3">
		<column id="name" num="1">name</column>
		<column id="quest" num="2">quest</column>
		<column id="colour" num="3">colour</column>
	</columns>
	<rows number="2">
		<c>Lancelot</c>
		<c>Grail</c>
		<c>blue</c>
		<c>Gallahad</c>
		<c>Seek Grail</c>
		<c>Blue! no, Red! Augh!</c>
	</rows>
</ezmatrix>
EOT;

        $doc = new DOMDocument( '1.0', 'utf-8' );
        self::assertTrue( $doc->loadXML( $storageFieldValue->dataText ) );

        $fieldValue = new FieldValue();

        $this->converter->toFieldValue( $storageFieldValue, $fieldValue );

        self::assertInternalType( 'array', $fieldValue->data );

        $matrixHash = $fieldValue->data;

        $columnNodes = $doc->getElementsByTagName( 'column' );

        $colSize = count( $matrixHash['columns'] );

        self::assertEquals( $colSize, $columnNodes->length );

        foreach ( $matrixHash['columns'] as $i => $column )
        {
            $columnNode = $columnNodes->item( $i );

            self::assertEquals(
                $columnNode->textContent,
                $column['name']
            );

            self::assertEquals(
                $columnNode->attributes->getNamedItem( 'id' )->nodeValue,
                $column['id']
            );

            self::assertEquals(
                $columnNode->attributes->getNamedItem( 'num' )->nodeValue,
                $column['num']
            );
        }

        $cNodes = $doc->getElementsByTagName( 'c' );

        foreach ( $cNodes as $i => $cNode )
        {
            //Get the correct row
            $rowNum = (int)floor( $i / $colSize );
            $rowKeys = array_keys( $matrixHash['rows'][$rowNum] );
            $rowValues = array_values( $matrixHash['rows'][$rowNum] );

            self::assertEquals( $cNode->textContent, $rowValues[$i % $colSize] );
            self::assertEquals(
                $columnNodes->item( $i % $colSize )->attributes->getNamedItem( 'id' )->nodeValue,
                $rowKeys[ $i % $colSize ]
            );
        }
    }
}
