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
    var $currentDate ;


    public function __construct(Document $structuredDocument, $config) {
        $this->structuredDocument = $structuredDocument;
        $this->xpath = new DOMXPath($structuredDocument);
        $this->cfg = $config;
        $this->currentDate = date("Y-m-d");
        parent::__construct('1.0', 'utf-8');
        $this->preserveWhiteSpace = true;
        $this->formatOutput = true;
        $this->isCorrectStructure();
        $this->getHeaders();
        $this->setBasicStructure();
        $this->setHeaders();
        $facsimiles = $this->xpath->query('//root/text/sec/title[text()="Facsimiles"]/parent::sec');
        $abstract = $this->xpath->query('//root/text/sec/title[text()="Abstract"]/parent::sec');
        $edition = $this->xpath->query('//root/text/sec/title[starts-with(text(),"Edition")]/parent::sec');
        $englishTranslation = $this->xpath->query('//root/text/sec/title[text()="English Translation"]/parent::sec');
        $synopsis = $this->xpath->query('//root/text/sec/title[text()="Synopsis"]/parent::sec');
        $translation = $this->xpath->query('//root/text/sec/title[text()="Translation"]/parent::sec');
        $commentry = $this->xpath->query('//root/text/sec/title[text()="Commentry"]/parent::sec');


    }

    private function getHeaders() : void {
        $metadataFields = $this->xpath->query('//root/text/sec/title[text()="Document metadata"]/parent::sec/table-wrap/table/row');
        foreach ($metadataFields as $metadata) {
            $cells = $metadata->getElementsByTagName("cell");
            if (count($cells) == 2) {
                $key = trim($cells->item(0)->nodeValue);
                $value = trim($cells->item(1)->textContent);
                if (!in_array($key, $this->cfg->headers)) {
                    $this->print_error("Unallowed header in the metadata " . $key);
                } else {
                    $this->headers[$key]=$value;
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
        $this->setEncodingDescription();
        $this->setProfileDescription();
        $this->setRevisionDescription();

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
        $this->setMsIdentifier($msDesc);
        $this->setAltIdentifier($msDesc);
        $this->setMsContents($msDesc);
        $this->createPhysicalDescription($msDesc);
        $this->createHistoryDescription($msDesc);

    }

    /**
     * @param \DOMElement $msDesc
     * @return array
     */
    private function setMsIdentifier(\DOMElement $msDesc): array {
        $msIdentifier = $this->createElement("msIdentifier");
        $settlement = $this->createElement("settlement", $this->headers["Place of deposit / current location of document"]);
        $repository = $this->createElement("repository", $this->headers["Deposit holding institution"]);
        $idno = $this->createElement("idno", $this->headers["Inventory number assigned by holding institution"]);
        $msIdentifier->appendChild($settlement);
        $msIdentifier->appendChild($repository);
        $msDesc->appendChild($msIdentifier);
        $msIdentifier->appendChild($idno);
        return array($settlement, $idno);
    }

    /**
     * @param \DOMElement $msDesc
     */
    private function setAltIdentifier(\DOMElement $msDesc): void {
        $altIdentifier = $this->createElement("altIdentifier");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'microfilm';
        $altIdentifier->appendChild($typeAttrib);
        $settlement = $this->createElement("settlement", $this->headers["Location"]);
        $collection = $this->createElement("collection", $this->headers["Alternative manifestation/inventory Type of manifestation"]);
        $idno = $this->createElement("idno", $this->headers["Inventory number"]);
        $altIdentifier->appendChild($settlement);
        $altIdentifier->appendChild($collection);
        $msDesc->appendChild($altIdentifier);
        $altIdentifier->appendChild($idno);
    }

    /**
     * @param \DOMElement $msDesc
     * @return array
     */
    private function setMsContents(\DOMElement $msDesc): array {
        $msContents = $this->createElement("msContents");
        $textLang = $this->createElement("textLang");
        $msContents->appendChild($textLang);
        $mainLang = $this->createAttribute('mainLang');
        $mainLang->value = $this->headers["Main language of document"];
        $textLang->appendChild($mainLang);
        $otherLangs = $this->createAttribute('otherLangs');
        $otherLangs->value = $this->headers["Other languages"];
        $textLang->appendChild($otherLangs);
        $msDesc->appendChild($msContents);
        return array($msContents, $textLang, $mainLang, $otherLangs);
    }

    /**
     * @param \DOMElement $msDesc
     * @return array
     */
    private function createPhysicalDescription(\DOMElement $msDesc): array {
        $phsyDesc = $this->createElement("phsyDesc");
        $p = $this->createDocumentFragment();
        $p->appendXML('<p>For details see <ref target="..">' . $this->headers["Link to catalogue entry"] . '</ref></p>');
        $phsyDesc->appendChild($p);
        $msDesc->appendChild($phsyDesc);
        return array($phsyDesc, $p);
    }

    /**
     * @param \DOMElement $msDesc
     */
    private function createHistoryDescription(\DOMElement $msDesc): void {
        $history = $this->createElement("history");
        $origin = $this->createElement("origin");
        $history->appendChild($origin);
        $p = $this->createDocumentFragment();
        $p->appendXML('<p>Issued in <origDate>' . $this->headers["Date of origin of document"] . '</origDate> from <origPlace>' . $this->headers["Place of origin of document"] . '</origPlace></p>');
        $origin->appendChild($p);
        $msDesc->appendChild($history);
    }

    private function setEncodingDescription(): void {
        $encodingDesc = $this->createDocumentFragment();
        $encodingDesc->appendXML("<encodingDesc><editorialDecl><p>The original document from which this e-text was formed is in the Devanāgarī script. The electronic text below contains the following parts: abstract, edition in Devanāgarī, English translation and optionally commentary.</p><p>The text as it appears in the original document is reproduced as faithfully as possible, including diacritic marks, such as the nukta (़); format features, such as line breaks; and graphical features, such as the middle dot (•) sporadically employed to mark word separation, or macrons and lines of various shapes, often used as placeholders or structuring elements. The editorial techniques applied introduce minimally invasive normalizations (by using <gi>orig</gi> and <gi>reg</gi> in<gi>choice</gi>) and corrections (by using <gi>sic</gi> and <gi>corr</gi> in<gi>choice</gi>). Words are separated by <gi>w</gi>, even if scriptura continua is used in the original documents. Furthermore, <gi>s</gi> is employed to indicate sentence like text units.</p></editorialDecl></encodingDesc>");
        $this->teiHeader->appendChild($encodingDesc);
    }
    private function setProfileDescription(): void {
        $profileDesc = $this->createDocumentFragment();
        $profileDesc->appendXML("<profileDesc><creation> <date>" . $this->currentDate . "</date> </creation> </profileDesc>");
        $this->teiHeader->appendChild($profileDesc);
    }

    private function setRevisionDescription(): void {
        $revisionDesc = $this->createDocumentFragment();
        $revisionDesc->appendXML('<revisionDesc><listChange> <change type="internal" when="'.$this->currentDate.'" who="#???????????">Automatically converted from docx to TEI-XML</change> </listChange> </revisionDesc>');
        $this->teiHeader->appendChild($revisionDesc);
    }

}