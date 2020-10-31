<?php


namespace docx2tei\tei;


class Headers extends \DOMDocument {
    var $document;
    public function __construct(TEIDocument  $document) {
        parent::__construct('1.0', 'utf-8');
        $this->document = $document;
        $this->getHeaders();
        $this->setHeaders();


    }

    function getHeaders(): void {
        $metadataFields = $this->xpath->query('//root/text/sec/title[text()="' . $this->cfg->sections->metadata . '"]/parent::sec/table-wrap/table/row');
        foreach ($metadataFields as $metadata) {
            $cells = $metadata->getElementsByTagName("cell");
            if (count($cells) == 2) {
                $headerName = trim($cells->item(0)->textContent);
                $value = trim($cells->item(1)->textContent);
                $config_headers = get_object_vars($this->cfg->headers);
                $key = array_search($headerName, array_values($config_headers));
                if ($key >= 0) {
                    $this->headers[array_keys($config_headers)[$key]] = $value;
                } else {
                    $this->print_error("[Error] Not allowed header in the metadata: " . $headerName);

                }

            } else {
                $this->print_error("Metadata table should be 2 columns wide");
            }
        }

    }
    function setHeaders() {

        $this->setFileDescription();
        $this->setSourceDescription();
        $this->setEncodingDescription();
        $this->setProfileDescription();
        $this->setRevisionDescription();

    }

    function setFileDescription(): void {
        $fileDesc = $this->createElement("fileDesc");
        $this->teiHeader->appendChild($fileDesc);
        $titleStmt = $this->createElement("titleStmt");
        $fileDesc->appendChild($titleStmt);
        $this->setHeaderTitle($titleStmt);
        $this->setShortTitle($titleStmt);
        $this->setSub($titleStmt);
        $this->setAuthor($titleStmt);
        $this->setMainEditor($titleStmt);
        $this->setCollaborator($titleStmt);
    }

    /**
     * @param DOMElement $titleStmt
     *//**/

    function setHeaderTitle(DOMElement $titleStmt): void {
        $mainTitle = $this->createElement("title", $this->headers["h12"] ?? "");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'main';
        $mainTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($mainTitle);
    }

    /**
     * @param DOMElement $titleStmt
     */
    function setShortTitle(DOMElement $titleStmt): void {
        $shortTitle = $this->createElement("title", $this->headers["h6"] ?? "");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'short';
        $shortTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($shortTitle);

    }

    /**
     * @param DOMElement $titleStmt
     */
    function setSub(DOMElement $titleStmt): void {
        $subTitle = $this->createElement("title", $this->headers["h4"] ?? "");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'sub';
        $subTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($subTitle);

    }

    /**
     * @param DOMElement $titleStmt
     */
    function setAuthor(DOMElement $titleStmt): void {
        $subTitle = $this->createElement("author", $this->headers["h2"] ?? "");
        $typeAttrib = $this->createAttribute('role');
        $typeAttrib->value = 'issuer';
        $subTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($subTitle);
    }

    /**
     * @param DOMElement $titleStmt
     */
    function setMainEditor(DOMElement $titleStmt): void {
        $respStmt = $this->createElement("respStmt");
        $resp = $this->createElement("resp", "main_editor");
        $respStmt->appendChild($resp);
        $name = $this->createElement("name", $this->headers["h13"] ?? "");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'main_editor';
        $name->appendChild($typeAttrib);
        $respStmt->appendChild($name);
        $titleStmt->appendChild($respStmt);
    }

    /**
     * @param DOMElement $titleStmt
     */
    function setCollaborator(DOMElement $titleStmt): void {
        $respStmt = $this->createElement("respStmt");
        $resp = $this->createElement("resp", "collaborator");
        $respStmt->appendChild($resp);
        $name = $this->createElement("name", $this->headers["h14"] ?? "");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'collaborator';
        $name->appendChild($typeAttrib);
        $respStmt->appendChild($name);
        $titleStmt->appendChild($respStmt);
    }

    function setSourceDescription(): void {
        $sourceDesc = $this->createElement("sourceDesc");
        $this->teiHeader->appendChild($sourceDesc);
        $msDesc = $this->createElement("msDesc");

        $sourceDesc->appendChild($msDesc);
        $this->setMsIdentifier($msDesc);
        $this->setAltIdentifier($msDesc);
        $this->setMsContents($msDesc);
        $this->setPhysicalDescription($msDesc);
        $this->setHistoryDescription($msDesc);

    }

    /**
     * @param DOMElement $msDesc
     * @return array
     */
    function setMsIdentifier(DOMElement $msDesc): array {
        $msIdentifier = $this->createElement("msIdentifier");
        $settlement = $this->createElement("settlement", $this->headers["h17"] ?? "");
        $repository = $this->createElement("repository", $this->headers["h5"] ?? "");
        $idno = $this->createElement("idno", $this->headers["h8"] ?? "");
        $msIdentifier->appendChild($settlement);
        $msIdentifier->appendChild($repository);
        $msDesc->appendChild($msIdentifier);
        $msIdentifier->appendChild($idno);
        return array($settlement, $idno);
    }
    /**
     * @param DOMElement $msDesc
     */
    function setAltIdentifier(DOMElement $msDesc): void {
        $altIdentifier = $this->createElement("altIdentifier");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = $this->headers["h1"] ?? "";
        $altIdentifier->appendChild($typeAttrib);
        $settlement = $this->createElement("settlement", $this->headers["h10"] ?? "");
        $collection = $this->createElement("collection", $this->headers["h19"] ?? "");
        $idno = $this->createElement("idno", $this->headers["h7"] ?? "");
        $altIdentifier->appendChild($settlement);
        $altIdentifier->appendChild($collection);
        $msDesc->appendChild($altIdentifier);
        $altIdentifier->appendChild($idno);
    }

    /**
     * @param DOMElement $msDesc
     * @return array
     */
    function setMsContents(DOMElement $msDesc): array {
        $msContents = $this->createElement("msContents");
        $textLang = $this->createElement("textLang");
        $msContents->appendChild($textLang);
        $mainLang = $this->createAttribute('mainLang');
        $mainLang->value = $this->headers["h11"] ?? "";
        $textLang->appendChild($mainLang);
        $otherLangs = $this->createAttribute('otherLangs');
        $otherLangs->value = $this->headers["h16"] ?? "";
        $textLang->appendChild($otherLangs);
        $msDesc->appendChild($msContents);
        return array($msContents, $textLang, $mainLang, $otherLangs);
    }

    /**
     * @param DOMElement $msDesc
     * @return array
     */
    function setPhysicalDescription(DOMElement $msDesc): array {
        $phsyDesc = $this->createElement("phsyDesc");
        $p = $this->createDocumentFragment();
        $catalogueEntry = $this->headers["h9"] ?? "";
        $p->appendXML('<p>For details see <ref target="..">' . $catalogueEntry . '</ref></p>');
        $phsyDesc->appendChild($p);
        $msDesc->appendChild($phsyDesc);
        return array($phsyDesc, $p);
    }

    /**
     * @param DOMElement $msDesc
     */
    function setHistoryDescription(DOMElement $msDesc): void {
        $history = $this->createElement("history");
        $origin = $this->createElement("origin");
        $history->appendChild($origin);
        $p = $this->createDocumentFragment() ?? "";
        $dateOfOrigin = $this->headers["h3"] . '</origDate> from <origPlace>' . $this->headers["h18"];
        $p->appendXML('<p>Issued in <origDate>' . $dateOfOrigin . '</origPlace></p>');
        $origin->appendChild($p);
        $msDesc->appendChild($history);
    }

    function setEncodingDescription(): void {
        $encodingDesc = $this->createDocumentFragment();
        $encodingDesc->appendXML("<encodingDesc><editorialDecl><p>The original document from which this e-text was formed is in the Devanāgarī script. The electronic text below contains the following parts: abstract, edition in Devanāgarī, English translation and optionally commentary.</p><p>The text as it appears in the original document is reproduced as faithfully as possible, including diacritic marks, such as the nukta (़); format features, such as line breaks; and graphical features, such as the middle dot (•) sporadically employed to mark word separation, or macrons and lines of various shapes, often used as placeholders or structuring elements. The editorial techniques applied introduce minimally invasive normalizations (by using <gi>orig</gi> and <gi>reg</gi> in<gi>choice</gi>) and corrections (by using <gi>sic</gi> and <gi>corr</gi> in<gi>choice</gi>). Words are separated by <gi>w</gi>, even if scriptura continua is used in the original documents. Furthermore, <gi>s</gi> is employed to indicate sentence like text units.</p></editorialDecl></encodingDesc>");
        $this->teiHeader->appendChild($encodingDesc);
    }

    function setProfileDescription(): void {
        $profileDesc = $this->createDocumentFragment();
        $profileDesc->appendXML("<profileDesc><creation> <date>" . $this->currentDate . "</date> </creation> </profileDesc>");
        $this->teiHeader->appendChild($profileDesc);
    }

    function setRevisionDescription(): void {
        $revisionDesc = $this->createDocumentFragment();
        $revisionDesc->appendXML('<revisionDesc><listChange> <change type="internal" when="' . $this->currentDate . '" who="#???????????">Automatically converted from docx to TEI-XML</change> </listChange> </revisionDesc>');
        $this->teiHeader->appendChild($revisionDesc);
    }


}