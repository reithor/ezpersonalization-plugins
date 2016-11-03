<?php

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

$filePath = dirname(__FILE__) . '/yoochoose-personalization-solution/yoochoose-personalization-solution.php';
$realPath = realpath($filePath);
if (!file_exists($realPath)) {
    exit('Error: File ' . $realPath . ' doesn\'t exist');
}

$matches = array();
$contents = file_get_contents($realPath);
preg_match("/\*\s*Version:\s*(.*)\n/i", $contents, $matches);
if (empty($matches)) {
    exit('Error: Version number not found!');
}

$version = trim($matches[1]);

$rootPath = dirname(__FILE__) . '/yoochoose-personalization-solution';
makeArchiveFile($rootPath, "yoochoose-woocommerce-$version.zip");