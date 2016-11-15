<?php

if (count($argv) === 1 || $argv[1] === '-h') {
    echo "\nMagento Packager Tool options:\n";
    echo "First parameter must be magento root source directory.\n";
    echo " -i : option to install and package magento plugin \n";
    echo " -d : option to delete magento plugin \n";
    echo " -c : option to delete magento plugin config data \n";
    echo " -u : option to do 'd' and 'c' \n";
    echo " -a : option to do all (uninstall, install and package)\n";
    exit;
}

if (!isset($argv[1]) && !filter_input(INPUT_GET, 'magentoSrc')) {
    exit("\nMagento root source directory MUST be specified!\n");
}

$deleteFiles = in_array('-a', $argv) || in_array('-d', $argv) || in_array('-u', $argv);
$deleteConfig = in_array('-a', $argv) || in_array('-c', $argv) || in_array('-u', $argv);
$install = in_array('-a', $argv) || in_array('-i', $argv);

$outputPath = __DIR__;
$currentDir = __DIR__;
$xmlName = $currentDir . DIRECTORY_SEPARATOR . 'xmlPackageStructure.xml';
$targetMap = array(
    array(
        'type' => 'file',
        'src' => './app/etc/modules/Yoochoose_JsTracking.xml',
    ),
    array(
        'type' => 'file',
        'src' => './app/design/frontend/base/default/layout/yoochoose_jstracking.xml',
    ),
    array(
        'type' => 'file',
        'src' => './app/design/frontend/base/default/template/yoochoose/head.phtml',
    ),
    array(
        'type' => 'dir',
        'src' => './app/code/community/Yoochoose/JsTracking',
    ),
    array(
        'type' => 'dir',
        'src' => './app/design/adminhtml/base/default/template/yoochoose',
    ),
);

$magentoRoot = isset($argv[1]) ? $argv[1] : urldecode(filter_input(INPUT_GET, 'magentoSrc'));

if (!is_dir($magentoRoot)) {
    exit('Magento path is not valid!');
}

// Change current directory to the directory of current script
chdir($magentoRoot);
require 'app/Mage.php';

Mage::app('admin')->setUseSessionInUrl(false);
umask(0);

/**
 * Reads XML file content
 *
 * @param string $name - name of the file
 * @return boolean|string - contents of file or false if file doesn't exist
 */
function readXmlFile($name)
{
    if (file_exists($name)) {
        return file_get_contents($name);
    }

    return false;
}

function deleteDir($dirPath)
{
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("Delete failed, $dirPath must be a directory");
    }

    $dirPath = rtrim($dirPath, '/') . '/';

    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }

    rmdir($dirPath);
}

function deleteConfig()
{
    echo "Deleting configuration started...\n";
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $table = $resource->getTableName('core/config_data');
    $query = "DELETE FROM $table WHERE path LIKE 'yoochoose%'";
    $writeConnection->query($query);
    echo "Deleting configuration finished successfully.\n";
}

function deletePluginFiles($targetMap)
{
    echo "Deleting plugin files started...\n";
    foreach ($targetMap as $target) {
        if ($target['type'] === 'file') {
            unlink($target['src']);
        } else if ($target['type'] === 'dir') {
            deleteDir($target['src']);
        }
    }

    echo "Deleting plugin files finished successfully.\n";
}

function copyNewPlugin($srcDir, $destDir)
{
    if (!is_dir($srcDir)) {
        throw new InvalidArgumentException("Copy failed, $srcDir must be a directory");
    }

    $srcDir = rtrim($srcDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

    $files = glob($srcDir . '*', GLOB_MARK);
    foreach ($files as $file) {
        $dirName = str_replace($srcDir, '', $file);
        $dirName = $destDir . DIRECTORY_SEPARATOR . $dirName;
        if (is_dir($file)) {
            if (!is_dir($dirName)) {
                mkdir($dirName);
            }

            copyNewPlugin($file, $dirName);
        } else {
            copy($file, $dirName);
        }
    }
}

try {
    if (Mage::isInstalled()) {
        //Deleting previous plugin files and configuration
        if ($deleteConfig) {
            deleteConfig();
        }

        if ($deleteFiles) {
            deletePluginFiles($targetMap);
        }
    }

    if ($install) {
        //Copying new plugin files to shop
        $start = DIRECTORY_SEPARATOR . 'app';
        echo "Copying plugin files started...\n";
        copyNewPlugin($currentDir . $start, $magentoRoot . $start);
        echo "Copying plugin files finished successfully.\n";
    }

    if (Mage::isInstalled()) {
        //Creating plugin archive
        echo "Generating package...\n";
        $configString = readXmlFile(realpath('./app/code/community/Yoochoose/JsTracking/etc/config.xml'));
        $configXml = simplexml_load_string($configString);
        $version = (string) $configXml->modules->Yoochoose_JsTracking->version;
        $xmlString = readXmlFile($xmlName);
        if ($xmlString === false) {
            throw new Exception('File "' . $xmlString . '" not found!');
        }

        $package = new Mage_Connect_Package($xmlString);
        $package->setVersion($version)
            ->setDate(date('Y-m-d'))
            ->setTime(date('H:i:s'))
            ->addContent('modules/Yoochoose_JsTracking.xml', 'mageetc')
            ->addContent('frontend/base/default/layout/yoochoose_jstracking.xml', 'magedesign')
            ->addContent('frontend/base/default/template/yoochoose/head.phtml', 'magedesign')
            ->addContentDir('magedesign', 'adminhtml/base/default/template/yoochoose')
            ->addContentDir('magecommunity', 'Yoochoose/JsTracking');

        $package->save($outputPath);
    } else {
        echo "Magento is not installed so package.xml could not be generated.\n";
    }

    echo "Success! Plugin archive is created at: '$outputPath'";
} catch (Exception $ex) {
    echo $ex->getMessage();
}
