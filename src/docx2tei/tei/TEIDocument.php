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
    var $headers = array();

    public function __construct(Document $structuredDocument, $config) {
        $this->structuredDocument = $structuredDocument;
        $this->xpath = new DOMXPath($structuredDocument);
        $this->cfg = $config;
        parent::__construct('1.0', 'utf-8');
        $this->preserveWhiteSpace = false;
        $this->formatOutput = true;
        $this->isCorrectStructure();

        $this->getHeaders();

        $this->setBasicStructure();
        $this->setHeaders();


    }

    private function getHeaders() : void {

        $metadataFields = $this->xpath->query("//root/text/sec[@id='sec-1']/table-wrap/table/row");
        foreach ($metadataFields as $metadata) {
            $cells = $metadata->getElementsByTagName("cell");
            if (count($cells) == 2) {
                $key = $cells->item(0)->nodeValue;
                $value = $cells->item(1)->textContent;
                if (!in_array($key, $this->cfg->headers)) {
                    $this->print_error("Unallowed header in the metadata " . $key);
                } else {
                    $this->headers[$key.""]=$value;
                }

            } else {
                $this->print_error("Metadata table should be 2 columns wide");
            }
        }

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
            "http://www.tei-c.org/ns/1.0"
        );
        $this->root->setAttribute('xml:id',$this->headers["Document ID"]);
        $this->appendChild($this->root);

        $this->teiHeader = $this->createElement('teiHeader');
        $this->root->appendChild($this->teiHeader);

        $this->text = $this->createElement('text');
        $this->root->appendChild($this->text);

    }

    private function setHeaders() {

        $this->setFileDescription();
        $this->setSourceDescription();

    }

    /**
     * @param \DOMElement $titleStmt
     *//**/
    private function createHeaderTitle(\DOMElement $titleStmt): void {
        $mainTitle = $this->createElement("title", $this->headers["Main title of document"]);
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'main';
        $mainTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($mainTitle);
    }

    /**
     * @param \DOMElement $titleStmt
     */
    private function createShortTitle(\DOMElement $titleStmt): void {
        $shortTitle = $this->createElement("title", $this->headers["Short title of document"]);
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'short';
        $shortTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($shortTitle);

    }

    /**
     * @param \DOMElement $titleStmt
     */
    private function createSub(\DOMElement $titleStmt): void {
        $subTitle = $this->createElement("title", $this->headers["Document ID"]);
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'sub';
        $subTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($subTitle);

    }

    /**
     * @param \DOMElement $titleStmt
     */
    private function createAuthor(\DOMElement $titleStmt): void {
        $subTitle = $this->createElement("author", $this->headers["Author/issuer of document"]);
        $typeAttrib = $this->createAttribute('role');
        $typeAttrib->value = 'issuer';
        $subTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($subTitle);
    }

    /**
     * @param \DOMElement $titleStmt
     */
    private function createMainEditor(\DOMElement $titleStmt): void {
        $respStmt = $this->createElement("respStmt");
        $resp = $this->createElement("resp", "main_editor");
        $respStmt->appendChild($resp);
        $name = $this->createElement("name", $this->headers["Name of editor(s)"]);
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'main_editor';
        $name->appendChild($typeAttrib);
        $respStmt->appendChild($name);
        $titleStmt->appendChild($respStmt);
    }
    /**
     * @param \DOMElement $titleStmt
     */
    private function createCollaborator(\DOMElement $titleStmt): void {
        $respStmt = $this->createElement("respStmt");
        $resp = $this->createElement("resp", "collaborator");
        $respStmt->appendChild($resp);
        $name = $this->createElement("name", $this->headers["Name of collaborator(s)"]);
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'collaborator';
        $name->appendChild($typeAttrib);
        $respStmt->appendChild($name);
        $titleStmt->appendChild($respStmt);
    }

    private function setFileDescription(): void {
        $fileDesc = $this->createElement("fileDesc");
        $this->teiHeader->appendChild($fileDesc);
        $titleStmt = $this->createElement("titleStmt");
        $fileDesc->appendChild($titleStmt);
        $this->createHeaderTitle($titleStmt);
        $this->createShortTitle($titleStmt);
        $this->createSub($titleStmt);
        $this->createAuthor($titleStmt);
        $this->createMainEditor($titleStmt);
        $this->createCollaborator($titleStmt);
    }

    private function setSourceDescription(): void {
        $sourceDesc = $this->createElement("sourceDesc");
        $this->teiHeader->appendChild($sourceDesc);
        $msDesc = $this->createElement("msDesc");
        $sourceDesc->appendChild($msDesc);
        $msIdentifier = $this->createElement("msIdentifier");
        $settlement = $this->createElement("settlement",$this->headers["Place of deposit / current location of document"]);
        $repository = $this->createElement("repository",$this->headers["Deposit holding institution"]);
        $idno = $this->createElement("idno",$this->headers["Inventory number assigned by holding institution"]);
        $msIdentifier->appendChild($settlement);
        $msIdentifier->appendChild($repository);
        $msDesc->appendChild($msIdentifier);
        $msIdentifier->appendChild($idno);

        $altIdentifier = $this->createElement("altIdentifier");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'microfilm';
        $altIdentifier->appendChild($typeAttrib);
        $settlement = $this->createElement("settlement",$this->headers["Location"]);
        $collection = $this->createElement("collection",$this->headers["Alternative manifestation/inventory Type of manifestation"]);
        $idno = $this->createElement("idno",$this->headers["Inventory number"]);
        $altIdentifier->appendChild($settlement);
        $altIdentifier->appendChild($collection);
        $msDesc->appendChild($altIdentifier);
        $altIdentifier->appendChild($idno);

    }

}