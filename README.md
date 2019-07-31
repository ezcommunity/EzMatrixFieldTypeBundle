EzMatrixFieldTypeBundle
=======================

[![Build Status](https://img.shields.io/travis/ezcommunity/EzMatrixFieldTypeBundle.svg?style=flat-square&branch=master)](https://travis-ci.org/ezcommunity/EzMatrixFieldTypeBundle)
[![Downloads](https://img.shields.io/packagist/dt/ezsystems/ez-matrix-bundle.svg?style=flat-square)](https://packagist.org/packages/ezsystems/ez-matrix-bundle)
[![Latest version](https://img.shields.io/github/release/ezcommunity/EzMatrixFieldTypeBundle.svg?style=flat-square)](https://github.com/ezcommunity/EzMatrixFieldTypeBundle/releases)
[![License](https://img.shields.io/github/license/ezcommunity/EzMatrixFieldTypeBundle.svg?style=flat-square)](LICENSE)

Bundle provides ezmatrix field type for eZ Publish Platform 5.x and higher

⛔️ **Status DEPRECATED:** As of 2.5 there is now bundled a [newer version](https://github.com/ezsystems/ezplatform-matrix-fieldtype) avaiable from eZ with full support, migration, UI editing and more. ⛔️

_This bundle can and should be used on earlier versions, but will not recive support for use in 1.x or 2.x UI or full API support, so UI editing is limited to use via legacy bridge/bundle._

### Install

From your [eZ Publish Platform](https://doc.ez.no/display/EZP/Installing+eZ+Publish+on+a+Linux-UNIX+based+system) install root with [composer installed](https://doc.ez.no/display/EZP/Using+Composer):

```
php -d memory_limit=-1 composer.phar require --prefer-dist ezsystems/ez-matrix-bundle:dev-master
```
Add the following in your `app/AppKernel.php` file:

```php
public function registerBundles()
{
    ...

    $bundles[] = new EzSystems\MatrixBundle\EzSystemsMatrixBundle();

    return $bundles;
}
```

### How to update content programmatically

Here's an example on how to update the value of a matrix field for a content item. The field has two columns, and we are creating two rows of content:

```php
$repository = $this->getContainer()->get( 'ezpublish.api.repository' );
$contentService = $repository->getContentService();

// This example for setting a current user is valid for 5.x and early versions of 6.x installs
// This is deprecated from 6.6, and you should use PermissionResolver::setCurrentUserReference() instead
$repository->setCurrentUser( $repository->getUserService()->loadUser( 14 ) );

$contentId = 26926;
$newTitle = 'My updated title 2';

try
{
    // create a content draft from the current published version
    $contentInfo = $contentService->loadContentInfo( $contentId );
    $contentDraft = $contentService->createContentDraft( $contentInfo );

    // instantiate a content update struct and set the new fields
    $contentUpdateStruct = $contentService->newContentUpdateStruct();
    $contentUpdateStruct->initialLanguageCode = 'eng-US'; // set language for new version
    $matrixValue = new \EzSystems\MatrixBundle\FieldType\Matrix\Value(
        array(
            new \EzSystems\MatrixBundle\FieldType\Matrix\Row( array( 'col1' => 'row1col1', 'col2' => 'row1col2' ) ),
            new \EzSystems\MatrixBundle\FieldType\Matrix\Row( array( 'col1' => 'row2col2', 'col2' => 'row2col2' ) ),
        ),
        array(
            new \EzSystems\MatrixBundle\FieldType\Matrix\Column( array( 'name' => 'Column 1', 'id' => 'col1', 'num' => 0 ) ),
            new \EzSystems\MatrixBundle\FieldType\Matrix\Column( array( 'name' => 'Column 2', 'id' => 'col2', 'num' => 1 ) ),
        )
    );
    $contentUpdateStruct->setField( 'title', $newTitle );
    $contentUpdateStruct->setField( 'matrix', $matrixValue );
    // update and publish draft
    $contentDraft = $contentService->updateContent( $contentDraft->versionInfo, $contentUpdateStruct );
    $content = $contentService->publishVersion( $contentDraft->versionInfo );
}
catch ( \eZ\Publish\API\Repository\Exceptions\NotFoundException $e )
{
    $output->writeln( $e->getMessage() );
}
catch( \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException $e )
{
    $output->writeln( $e->getMessage() );
}
catch( \eZ\Publish\API\Repository\Exceptions\ContentValidationException $e )
{
    $output->writeln( $e->getMessage() );
}
```

### License & Copyright

See LICENSE file.


### Contributors 

https://github.com/ezcommunity/EzMatrixFieldTypeBundle/graphs/contributors
