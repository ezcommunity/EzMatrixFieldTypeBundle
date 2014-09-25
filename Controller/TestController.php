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

namespace EzSystems\MatrixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TestController extends Controller
{
    public function indexAction( $contentId )
    {
        $repository = $this->get( 'ezpublish.api.repository' );
        $contentService = $repository->getContentService();
        $contentTypeService = $repository->getContentTypeService();
        $fieldTypeService = $repository->getFieldTypeService();

        try
        {
            $content = $contentService->loadContent( $contentId );
            $contentType = $contentTypeService->loadContentType( $content->contentInfo->contentTypeId );
            foreach( $contentType->fieldDefinitions as $fieldDefinition )
            {
                if ( $fieldDefinition->fieldTypeIdentifier == 'ezmatrix' )
                {
                    //$fieldType = $fieldTypeService->getFieldType( $fieldDefinition->fieldTypeIdentifier );
                    $field = $content->getField( $fieldDefinition->identifier );

                    return $this->render(
                        'EzSystemsMatrixBundle::content_fields.html.twig',
                        array( 'field' => $field ) );
                }

            }
            return "No ezmatrix attribute found for content $contentId";
        }
        catch ( \eZ\Publish\API\Repository\Exceptions\NotFoundException $e )
        {
            return "<error>No content with id $contentId found</error>";
        }
        catch ( \eZ\Publish\API\Repository\Exceptions\UnauthorizedException $e )
        {
            return "<error>Permission denied on content with id $contentId</error>";
        }
    }
}
