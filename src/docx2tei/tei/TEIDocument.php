<?php namespace docx2tei\tei;


use docx2tei\structure\Document;
use DOMDocument;
use DOMXPath;

class TEIDocument extends DOMDocument {
    var $cfg;
    var $root;
    var $teiHeader;
    var $text;
    var $back;
    var $structuredDocument;
    var $xpath;

    public function __construct(Document $structuredDocument, $config) {
        $this->structuredDocument = $structuredDocument;
        $this->xpath = new DOMXPath($structuredDocument);
        $this->cfg = $config;
        parent::__construct('1.0', 'utf-8');
        $this->preserveWhiteSpace = false;
        $this->formatOutput = true;
        $this->isCorrectStructure();
        $this->setBasicStructure();
        $this->setHeaders();


    }

    private function getHeaders() :array {
        $headers = array();
        $metadataFields = $this->xpath->query("//root/text/sec[@id='sec-1']/table-wrap/table/row");
        foreach ($metadataFields as $metadata) {
            $cells = $metadata->getElementsByTagName("cell");
            if (count($cells) == 2) {
                $key = $cells->item(0)->nodeValue;
                $value = $cells->item(1)->textContent;
                if (!in_array($key, $this->cfg->headers)) {
                    $this->print_error("Unallowed header in the metadata " . $key);
                } else {
                    $headers[] [$key] = $value;
                }

            } else {
                $this->print_error("Metadata table should be 2 columns wide");
            }
        }
        return $headers;
    }

    public function saveToFile(string $pathToFile) {
        $this->save($pathToFile);
    }

    private function isCorrectHeaders(): bool {
        return true;
    }

    /**
     * @return bool
     */
    private function isCorrectSections(): bool {
        $sectionNodes = $this->xpath->query("//root/text/sec/title");
        foreach ($sectionNodes as $section) {
            if (!in_array($section->nodeValue, $this->cfg->sections)) {
                // specially handle Editions
                if (!preg_match("/Edition(\s)*\((.)*\)/i", $section->nodeValue)) {
                    $this->print_error("Section missing or wrong : " . $section->nodeValue);
                    return false;
                }
            }
        }
        return true;
    }

    private function isCorrectStructure(): bool {
        $correct = true;
        $correct = $this->isCorrectSections();
        $correct = $this->isCorrectHeaders();

        return $correct;

    }

    /**
     * @param $value
     */
    private function print_error($message): void {
        echo("" . $message . "\n");
        //error_log($message."\n");
    }

    private function setBasicStructure() {

        $this->root = $this->createElement('TEI');
        $this->root->setAttributeNS(
            "http://www.w3.org/2000/xmlns/",
            "xmlns",
            "http://www.structure-c.org/ns/1.0"
        );
        $this->appendChild($this->root);

        $this->teiHeader = $this->createElement('teiHeader');
        $this->root->appendChild($this->teiHeader);

        $this->text = $this->createElement('text');
        $this->root->appendChild($this->text);

    }

    private function setHeaders() {
        $headers = $this->getHeaders();
        $fileDesc = $this->createElement("fileDesc");
        $this->teiHeader->appendChild($fileDesc);
        $titleStmt = $this->createElement("titleStmt");
        $fileDesc->appendChild($titleStmt);
        //<title type="main"></title>
        $mainTitle = $this->createElement("title", "default");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'main';
        $mainTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($mainTitle);
        //<title type="short"></title>
        $shortTitle = $this->createElement("title", "default");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'short';
        $shortTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($shortTitle);

        //<title type="sub"></title>
        $subTitle = $this->createElement("title", "default");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'sub';
        $subTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($subTitle);
    }

}