<?php
/**
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://ez.no/eZPublish/Licenses/eZ-Trial-and-Test-License-Agreement-eZ-TTL-v2.0 eZ Trial and Test License Agreement Version 2.0
 */

namespace EzSystems\MatrixBundle\Persistence\Legacy\Content\FieldValue\Converter;

use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter,
    eZ\Publish\SPI\Persistence\Content\FieldValue,
    eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue,
    eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition,
    eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition,
    eZ\Publish\Core\FieldType\FieldSettings,
    DOMDocument;

class Matrix implements Converter
{
    /**
     * Factory for current class
     *
     * @note Class should instead be configured as service if it gains dependencies.
     *
     * @static
     * @return Url
     */
    public static function create()
    {
        return new self;
    }

    /**
     * Converts data from $value to $storageFieldValue
     *
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $value
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue $storageFieldValue
     */
    public function toStorageValue( FieldValue $value, StorageFieldValue $storageFieldValue )
    {
        $storageFieldValue->dataText = $this->generateXmlString( $value->data );
    }

    /**
     * Converts data from $value to $fieldValue
     *
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue $value
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $fieldValue
     */
    public function toFieldValue( StorageFieldValue $value, FieldValue $fieldValue )
    {
        $fieldValue->data = ( $value->dataText ? $this->restoreFromXmlString( $value->dataText ) : Value::EMPTY_VALUE );
    }

    /**
     * Converts field definition data in $fieldDef into $storageFieldDef
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDef
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition $storageDef
     */
    public function toStorageFieldDefinition( FieldDefinition $fieldDef, StorageFieldDefinition $storageDef )
    {
        /// @todo what about empty value???
        $storageDefinition->dataText5 = $this->generateDefinitionXmlString( $fieldDef->fieldTypeConstraints->fieldSettings['columns'] );
        $storageDef->dataInt1 = $fieldDef->fieldTypeConstraints->fieldSettings['defaultRows'];
    }

    /**
     * Converts field definition data in $storageDef into $fieldDef
     *
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition $storageDef
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDef
     */
    public function toFieldDefinition( StorageFieldDefinition $storageDef, FieldDefinition $fieldDef )
    {
        $cols = array();
        if ( !empty( $storageDefinition->dataText5 ) )
        {
            $cols = $this->restoreDefinitionFromXmlString( $storageDefinition->dataText5 );
        }

        $fieldDef->fieldTypeConstraints->fieldSettings = new FieldSettings(
            array(
                'defaultRows' => $storageDef->dataInt1,
                'columns' => $cols,
                //'name' => '',
            )
        );
    }

    /**
     * Returns the name of the index column in the attribute table
     *
     * Returns the name of the index column the datatype uses, which is either
     * "sort_key_int" or "sort_key_string". This column is then used for
     * filtering and sorting for this type.
     *
     * @return false
     */
    public function getIndexColumn()
    {
        return false;
    }

    protected function generateXmlString( array $matrixValue )
    {
        $domDoc = new DOMDocument( '1.0', 'utf-8' );
        $root =  $domDoc->createElement( 'ezmatrix' );
        $nameElement = $domDoc->createElement( 'name' );
        $nameElement->appendchild( $domDoc->createTextNode( $matrixValue['name'] ) );
        $root->appendchild( $nameElement );
        $colsElement = $domDoc->createElement( 'columns' );
        $colsElement->setAttribute( "number", count( $matrixValue['columns'] ) );
        $root->appendchild( $colsElement );
        foreach( $matrixValue['columns'] as $idx => $colDef )
        {
            $colElement = $domDoc->createElement( 'column', $colDef['name'] );
            $colElement->setAttribute( 'id', $colDef['identifier'] );
            $colElement->setAttribute( 'num', $idx );
            $colsElement->appendchild( $colElement );
        }
        $rowsElement = $domDoc->createElement( 'rows' );
        $rowsElement->setAttribute( "number", count( $matrixValue['rows'] ) );
        $root->appendchild( $rowsElement );
        foreach( $matrixValue['rows'] as $idx => $rowDef )
        {
            foreach ( $rowDef as $data )
            {
                $dataElement = $domDoc->createElement( 'c', $data );
                $root->appendchild( $dataElement );
            }
        }
        $domDoc->appendChild( $root );
        return $domDoc->saveXML();
    }

    /**
     * @param string $xmlstring
     * @todo throw exception on invalid xml?
     */
    protected function restoreFromXmlString( $xmlString )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $columns = array();
        $rows = array();
        $name = '';

        if ( $dom->loadXML( $xmlString ) === true )
        {
            foreach ( $dom->getElementsByTagName( 'name' ) as $name )
            {
                $name = $name->textContent;
                break;
            }

            foreach ( $dom->getElementsByTagName( 'column' ) as $colDef )
            {
                $columns[$colDef->getAttribute( 'num' )] = array(
                    'name' => $colDef->textContent,
                    'identifier' => $colDef->getAttribute( 'id' )
                );
            }
            $colcount = count( $columns );
            $colindexes = array_keys( $columns );

            // Q: Are these validations necessary here, or is this left to FieldType?
            /// @todo validate $colcount vs. "number" attribute of "columns" element
            /// @todo validate: $colcount != 0

            $row = array();
            $i = 0;
            foreach ( $dom->getElementsByTagName( 'c' ) as $data )
            {
                $row[$colindexes[$i++]] = $data->textContent;
                if ( $i == $colcount )
                {
                    $rows[] = $row;
                    $row = array();
                    $i = 0;
                }
            }

            /// @todo validate: $rowcount vs. "number" attribute of "rows" element
        }

        return array( 'rows' => $rows, 'columns' => $columns, 'name' => $name );
    }

    protected function generateDefinitionXmlString( array $colDefs )
    {
        $domDoc = new DOMDocument( '1.0', 'utf-8' );
        $root =  $domDoc->createElement( 'ezmatrix' );
        foreach( $colDefs as $idx => $colDef )
        {
            $colElement = $domDoc->createElement( 'column-name', $colDef['name'] );
            $colElement->setAttribute( 'id', $colDef['identifier'] );
            $colElement->setAttribute( 'idx', $idx );
            $root->appendchild( $colElement );
        }
        $domDoc->appendChild( $root );
        return $domDoc->saveXML();
    }

    /**
     * @param string $xmlstring
     * @todo throw exception on invalid xml?
     */
    protected function restoreDefinitionFromXmlString( $xmlString )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $columns = array();

        if ( $dom->loadXML( $xmlString ) === true )
        {
            foreach ( $dom->getElementsByTagName( 'column-name' ) as $coldef )
            {
                $columns[$coldef->getAttribute( 'idx' )] = array(
                    'identifier' => $coldef->getAttribute( 'id' ),
                    'name' => $coldef->textContent
                );
            }
        }

        return $columns;
    }
}
