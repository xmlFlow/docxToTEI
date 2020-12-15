<?php


$time_start = microtime(true);
error_reporting(E_ALL ^ E_WARNING);

require_once(__DIR__ . "/vendor/autoload.php");

use docx2tei\DOCXArchive;
use docx2tei\structure\Document;


//ini_set('error_log', 'errors.log');


$config = null;

$inputPath = null;
$outputPath = null;
$configFile = null;


if ($argc == 4) {
    $inputPath = $argv[1];
    $outputPath = $argv[2];
    $configFile = $argv[3];
} else {
    throw new InvalidArgumentException("requires valid input and output paths" . "\n" .
        "Basic usage: php docxtotei.php [path/to/file.docx or path/to/input/dir] [path/to/output/file.xml or path/to/output/dir] [path/to/config.json]" . "\n");
}
if (file_exists($configFile)) {
    $data = file_get_contents($configFile);
    $config = json_decode($data);

} else {
    echo("configuration file: config.json not found");
}

$inputPathParts = pathinfo($inputPath);
if (array_key_exists("extension", $inputPathParts) && $inputPathParts["extension"] == "docx") {
    $inputs["singleFile"] = $inputPath;
} elseif (is_dir($inputPath)) {
    $inputs = scandir($inputPath);
} else {
    throw new UnexpectedValueException("the input must be a file with extension .docx or existing directory");
}

$outputPathParts = pathinfo($outputPath);

if (!array_key_exists("extension", $outputPathParts)) {
    $outputDir = $outputPath;
} else {
    $outputDir = $outputPathParts["dirname"];
}

$outputDir = rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

if (array_key_exists("singleFile", $inputs)) {
    writeOutput($inputPath, $outputPathParts, $inputPathParts, $outputDir, false, $config);
} else {
    foreach ($inputs as $input) {
        $inputFilePath = rtrim($inputPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $input;
        $inputFilePathParts = pathinfo($inputFilePath);
        if (array_key_exists("extension", $inputFilePathParts) && $inputFilePathParts["extension"] == "docx") {
            writeOutput($inputFilePath, $outputPathParts, $inputFilePathParts, $outputDir, true, $config);
        }
    }
}
function arrayRecursiveDiff($aArray1, $aArray2) {
    $aReturn = array();

    foreach ($aArray1 as $mKey => $mValue) {
        if (array_key_exists($mKey, $aArray2)) {
            if (is_array($mValue)) {
                $aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                if (count($aRecursiveDiff)) {
                    $aReturn[$mKey] = $aRecursiveDiff;
                }
            } else {
                if ($mValue != $aArray2[$mKey]) {
                    $aReturn[$mKey] = $mValue;
                }
            }
        } else {
            $aReturn[$mKey] = $mValue;
        }
    }
    return $aReturn;
}

function getChangeElements($currentDoc, $oldContent) {
    try {

       /* $oldXML = file_get_contents($oldContent);
        $oldXMLNOde = simplexml_load_string($oldXML, "SimpleXMLElement", LIBXML_NOCDATA);
        $oldXMLjson = json_encode($oldXMLNOde);
        $oldXMLArray = json_decode($oldXMLjson, TRUE);
        $currentXML = $currentDoc->saveXML();
        $currentXMLNOde = simplexml_load_string($currentXML, "SimpleXMLElement", LIBXML_NOCDATA);
        $currentXMLjson = json_encode($currentXMLNOde);
        $currentXMLArray = json_decode($currentXMLjson, TRUE);
        $results = arrayRecursiveDiff($currentXMLArray,$oldXMLArray);
        exec("git diff", $output, $retval);
        var_dump($output);
       */

    } catch (Exception $ex) {
        throw $ex;
    }
}

function writeOutput(string $inputFilePath, array $outputPathParts, array $inputPathParts, string $outputDir, bool $isDir, $config): void {
    $time_start = microtime(true);
    $docxArchive = new DOCXArchive($inputFilePath);
    $structuredXML = new Document($docxArchive);

    $teiDocument = new docx2tei\tei\TEIDocument($structuredXML, $config);
    //$teiDocument = new Document($docxArchive);
    if (array_key_exists("extension", $outputPathParts) && !$isDir) {
        $filename = $outputPathParts["filename"];
    } else {
        $filename = $inputPathParts["filename"];
    }

    if (!$isDir) {
        $filePath = $outputDir . $filename . ".xml";
        //$structuredXML->saveToFile($filePath);
        getChangeElements($teiDocument, $filePath);
        $teiDocument->saveToFile($filePath);
        $docxArchive->getMediaFiles($outputDir);
    } else {
        if (!is_dir($outputDir . $filename)) {
            mkdir($outputDir . $filename);
        }
        $dirFilePath = $outputDir . $filename . DIRECTORY_SEPARATOR . $filename . ".xml";
        //$structuredXML->saveToFile($dirFilePath);
        $teiDocument->save($dirFilePath);
        $docxArchive->getMediaFiles($outputDir . $filename . DIRECTORY_SEPARATOR);
    }

}

$time_end = microtime(true);
print("[Info] Conversion successful");
