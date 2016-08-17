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

$filePath = dirname(__FILE__) . '/app/code/Yoochoose/Tracking/composer.json';
$realPath = realpath($filePath);
if (!file_exists($realPath)) {
    exit('Error: File ' . $realPath . ' doesn\'t exist');
}

$contents = file_get_contents($realPath);
$parsed = json_decode($contents, true);
$version = $parsed['version'];

$rootPath = dirname(__FILE__) . '/app/code/Yoochoose';
makeArchiveFile($rootPath, "yoochoose_tracking-$version.zip");