<?php



require_once (__DIR__ . "/vendor/autoload.php");

use docx2tei\DOCXArchive;
use docx2tei\structure\Document;

// Parsing DOCX
$path = "samples/input/example.docx";
$docxArchive = new DOCXArchive($path);

$contents = $docxArchive->getDocument()->getContent();

foreach ($contents as $content) {
	//echo "\n";
	if (get_class($content) === "docx2tei\objectModel\body\Par") {
		foreach ($content->getContent() as $textObject) {
			//echo $textObject->getContent();
		}
		//echo $content->getNumberingId();
	} elseif (get_class($content) === "docx2tei\objectModel\body\Table") {
		foreach ($content->getContent() as $textObject) {
			//echo $textObject->getContent();
		}
	}
}

// Creating JATS XML
$teiXML = new Document($docxArchive);
$filename = basename($path, ".docx");
$outputDir = "samples/output/";
$teiXML->saveToFile($outputDir . $filename . ".xml");
$docxArchive->getMediaFiles($outputDir);
