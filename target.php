<?php
namespace dreamwhiteAPIv1;

//require_once 'includes.php';

$path = __DIR__ . "/entities/counterparty/files/";

ini_set("display_errors", 0);

$files = array_diff(scandir($path), array('..', '.'));

//var_dump($files);

$zipname = 'targets.zip';
$zip = new \ZipArchive;
$zip->open($zipname, \ZipArchive::CREATE);

foreach ($files as $file) {
    $filePath = $path . $file;
    if(file_exists($filePath)){
        $zip->addFromString(basename($filePath),  file_get_contents($filePath));
    }
}
$zip->close();

//$zipname = 'readme.txt';

header('Content-Description: File Transfer');
header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipname .'"');
header('Content-Length: ' . filesize($zipname));
flush();
readfile($zipname);