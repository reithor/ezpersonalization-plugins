<?php

// Change current directory to the directory of current script
chdir(dirname(__FILE__));

require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo "Application is not installed yet, please complete install wizard first.";
    exit;
}

// Only for urls
// Don't remove this
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);

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

try {
    $version = Mage::helper('yoochoose_jstracking')->getModuleVersion();
    $xmlName = (isset($argv[1]) && file_exists($argv[1])) ? $argv[1] : 'xmlPackageStructure.xml';
    $outputPath = (isset($argv[2]) && file_exists($argv[2])) ? $argv[2] : __DIR__;

    $xmlString = readXmlFile($xmlName);
    if ($xmlString === false) {
        throw new Exception('File "' . $name . '" not found!');
    }

    $package = new Mage_Connect_Package($xmlString);
    $package->setVersion($version)
            ->setDate(date('Y-m-d'))
            ->setTime(date('H:i:s'))
            ->addContent('modules/Yoochoose_JsTracking.xml', 'mageetc')
            ->addContent('frontend/base/default/layout/yoochoose_jstracking.xml', 'magedesign')
            ->addContentDir('magedesign', 'adminhtml/base/default/template/yoochoose')
            ->addContentDir('magecommunity', 'Yoochoose');

    $package->save($outputPath);
    echo 'Success';

} catch (Exception $ex) {
    echo $ex->getMessage();
}