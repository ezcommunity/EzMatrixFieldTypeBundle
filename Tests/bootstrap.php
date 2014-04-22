<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joe
 * Date: 3/8/13
 * Time: 6:05 AM
 * To change this template use File | Settings | File Templates.
 */

$filename = __DIR__ .'/../../vendor/autoload.php';

if (!file_exists($filename)) {
    throw new Exception("You need to execute `composer install` before running the tests. (vendors are required for test execution)");
}

require_once $filename;