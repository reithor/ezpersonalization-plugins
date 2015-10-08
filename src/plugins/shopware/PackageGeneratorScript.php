<?php

/**
 * Class Shopware_Components_Plugin_Bootstrap
 *
 * Mock class just so instance of Shopware_Plugins_Frontend_YoochooseJsTracking_Bootstrap can be created
 * so version number could be retrieved
 */
class Shopware_Components_Plugin_Bootstrap
{
}

function makeArchiveFile($content, $destination)
{
    $zip = new ZipArchive();
    $zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    $content  = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, realpath($content));
    $index    = strrpos($content, DIRECTORY_SEPARATOR) + 1;
    $content .= DIRECTORY_SEPARATOR;
    $stack    = array($content);
    while (!empty($stack) ) {
        $current = array_pop($stack);
        $files   = array();

        $dir = dir($current);
        while (($node = $dir->read()) !== false) {
            if ($node == '.' || $node == '..') {
                continue;
            }

            if (is_dir($current . $node)) {
                array_push($stack, $current . $node . DIRECTORY_SEPARATOR);
            }

            if (is_file($current . $node)) {
                $files[] = $node;
            }
        }

        $local = str_replace('\\', '/', substr($current, $index));
        $zip->addEmptyDir(substr($local, 0, -1));

        foreach ($files as $file) {
            $zip->addFile($current . $file, $local . $file);
        }
    }

    $zip->close();
}

require_once 'Frontend/YoochooseJsTracking/Bootstrap.php';

/**
 * @var Shopware_Plugins_Frontend_YoochooseJsTracking_Bootstrap $bootstrap
 */
$bootstrap = new Shopware_Plugins_Frontend_YoochooseJsTracking_Bootstrap();
$version = $bootstrap->getVersion();
$rootPath = dirname(__FILE__) . '/Frontend';

makeArchiveFile($rootPath, "yoochoose-shopware-$version.zip");