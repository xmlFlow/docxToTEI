<?php namespace docx2tei\tei;


use docx2tei\structure\Document;

class TEIDocument extends \DOMDocument {
    var $cfg;
    var $root;
    var $teiHeader;
    var $text;
    var $back;
    var $structuredDocument;
    var $xpath;

    public function __construct(Document $structuredDocument, $config) {
        $this->structuredDocument = $structuredDocument;
        $this->xpath = new \DOMXPath($structuredDocument);
        $this->cfg = $config;
        $tmpStr = $this->structuredDocument->saveXML();
        parent::__construct('1.0', 'utf-8');
        $this->preserveWhiteSpace = false;
        $this->formatOutput = true;
        $this->checkStructure();
        $this->setBasicStructure();
        $this->createHEIHeader();


    }

    private function checkStructure() {
        $sectionNodes = $this->xpath->query("//root/text/sec/title");
        foreach($sectionNodes as $section) {
            if (!in_array($section->nodeValue,$this->cfg->sections)){
                // specially handle Editions
                if (!preg_match("/Edition(\s)*\((.)*\)/i",$section->nodeValue)) {
                    $this->print_error("Section missing or wrong : " . $section->nodeValue);
                }
            };
        }


    }

    private function setBasicStructure() {

        $this->root = $this->createElement('TEI');
        $this->root->setAttributeNS(
            "http://www.w3.org/2000/xmlns/",
            "xmlns",
            "http://www.structure-c.org/ns/1.0"
        );
        // $this->structure->setAttributeNS("http://www.w3.org/2000/xmlns/", "xmlns:xlink", "http://www.w3.org/1999/xlink");

        $this->appendChild($this->root);

        $this->teiHeader = $this->createElement('teiHeader');
        $this->root->appendChild($this->teiHeader);

        $this->text = $this->createElement('text');
        $this->root->appendChild($this->text);

    }
    private function createHEIHeader() {
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


    public function getDocument(string $pathToFile) {
        $this->save($pathToFile);
        }

    /**
     * @param $value
     */
    private function print_error($message): void {
        error_log(":: ".$message);
        echo "".$message."\n";
    }

}