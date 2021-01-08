<?php

namespace docx2tei\tei;

use docx2tei\XMLUtils;
use DOMDocument;

class Headers extends DOMDocument {
    var $document;
    var $headers;
    var $currentDate;
    var $currentYear;

    public function __construct(TEIDocument $document, $headers) {
        parent::__construct('1.0', 'utf-8');
        $this->currentDate = date("Y-m-d");
        $this->currentYear = date("Y");
        $this->document = $document;
        $this->headers = $headers;
        $this->setHeaders();
    }

    function setHeaders() {
        $this->setFileDescription();
        $this->setEncodingDescription();
        $this->setProfileDescription();
        $this->setRevisionDescription();
    }

    function setFileDescription(): void {
        $fileDesc = $this->createElement("fileDesc");
        $titleStmt = $this->createElement("titleStmt");
        $fileDesc->appendChild($titleStmt);
        $this->setHeaderTitle($titleStmt);
        $this->setShortTitle($titleStmt);
        $this->setSub($titleStmt);
        $this->setAuthor($titleStmt);
        $this->setMainEditor($titleStmt);
        $this->setCollaborator($titleStmt);
        $publicationStmt = $this->createElement("publicationStmt");
        $fileDesc->appendChild($publicationStmt);
        $this->setPublicationStmt($publicationStmt);
        $this->setNotesStmt($fileDesc);
        $this->setSourceDescription($fileDesc);
        $this->document->teiHeader->appendChild($this->document->importNode($fileDesc, true));
    }

    function setNotesStmt($pubStmt):void {

        $noteStmt = $this->createElement("notesStmt");
        $note = $this->createElement("note",$this->headers["h21"] ?? "");
        $noteStmt->appendChild($note);
        $pubStmt->appendChild($noteStmt);

    }

    function setEncodingDescription(): void {
        $encodingDesc = $this->createDocumentFragment();
        $encodingDesc->appendXML("<encodingDesc><editorialDecl><p>The original document from which this e-text was formed is in the Devanāgarī script. The electronic text below contains the following parts: abstract, edition in Devanāgarī, English translation and optionally commentary.</p><p>The text as it appears in the original document is reproduced as faithfully as possible, including diacritic marks, such as the nukta (़); format features, such as line breaks; and graphical features, such as the middle dot (•) sporadically employed to mark word separation, or macrons and lines of various shapes, often used as placeholders or structuring elements. The editorial techniques applied introduce minimally invasive normalizations (by using <gi>orig</gi> and <gi>reg</gi> in<gi>choice</gi>) and corrections (by using <gi>sic</gi> and <gi>corr</gi> in<gi>choice</gi>). Words are separated by <gi>w</gi>, even if scriptura continua is used in the original documents. Furthermore, <gi>s</gi> is employed to indicate sentence like text units.</p></editorialDecl></encodingDesc>");
        $this->document->teiHeader->appendChild($this->document->importNode($encodingDesc, true));
    }
    /**
     * @param  $pubStmt
     *//**/

    function setProfileDescription(): void {
        $profileDesc = $this->createDocumentFragment();
        $profileDesc->appendXML("<profileDesc><creation> <date>" . $this->currentDate . "</date> </creation> </profileDesc>");
        $this->document->teiHeader->appendChild($this->document->importNode($profileDesc, true));
    }

    function setRevisionDescription(): void {
        $revisionDesc = $this->createDocumentFragment();
        $revisionDesc->appendXML('<revisionDesc><listChange> <change type="internal" when="' . $this->currentYear . '" who="#AUTO">Automatically converted from docx to TEI-XML</change> </listChange> </revisionDesc>');
        $this->document->teiHeader->appendChild($this->document->importNode($revisionDesc, true));
    }

    function setHeaderTitle($titleStmt): void {
        $mainTitle = $this->createElement("title", $this->headers["h12"] ?? "");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'main';
        $mainTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($mainTitle);
    }

    /**
     * @param  $titleStmt
     */
    function setShortTitle($titleStmt): void {
        $shortTitle = $this->createElement("title", $this->headers["h6"] ?? "");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'short';
        $shortTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($shortTitle);
    }

    /**
     * @param  $titleStmt
     */
    function setSub($titleStmt): void {
        $subTitle = $this->createElement("title", $this->headers["h4"] ?? "");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = 'sub';
        $subTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($subTitle);
    }

    /**
     * @param  $titleStmt
     */
    function setAuthor($titleStmt): void {
        $subTitle = $this->createElement("author", $this->headers["h2"] ?? "");
        $typeAttrib = $this->createAttribute('role');
        $typeAttrib->value = 'issuer';
        $subTitle->appendChild($typeAttrib);
        $titleStmt->appendChild($subTitle);
    }

    /**
     * @param  $titleStmt
     */
    function setMainEditor($titleStmt): void {
        $respStmt = $this->createElement("respStmt");
        $editorTypeString = "";
        $editorType = "";
        $etSec = $this->document->xpath->query('//root/text/sec/title[text()="' . $this->document->cfg->sections->et . '"]');
        $synSec = $this->document->xpath->query('//root/text/sec/title[text()="' . $this->document->cfg->sections->synopsis . '"]');
        if (count($etSec) != 0) {
            $editorTypeString = 'main editor and translator';
            $editorType = 'main_editor';
        }
        if (count($synSec) != 0) {
            $editorTypeString = 'main editor';
            $editorType = 'synopsis_editor';
        }
        if (count($etSec) + count($synSec) == 2) {
            XMLUtils::print_error('[Warning] both English translation and synopsis defined. Please define only one of them.');
        }
        $resp = $this->createElement("resp", $editorTypeString);
        $respStmt->appendChild($resp);
        $name = $this->createElement("name", $this->headers["h13"] ?? "");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = $editorType;
        $name->appendChild($typeAttrib);
        $respStmt->appendChild($name);
        $titleStmt->appendChild($respStmt);
    }

    /**
     * @param  $titleStmt
     */
    function setCollaborator($titleStmt): void {
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

    function setPublicationStmt($pubStmt): void {
        $publisher = $this->createElement("publisher", "Heidelberg Academy of Sciences and Humanities: Documents on the History of Religion and Law of Pre-modern Nepal");
        $pubStmt->appendChild($publisher);
        $pubPlace = $this->createElement("pubPlace", "Heidelberg, Germany");
        $pubStmt->appendChild($pubPlace);
        $availability = $this->createElement("availability");
        $statusAttrib = $this->createAttribute('status');
        $statusAttrib->value = 'restricted';
        $availability->appendChild($statusAttrib);
        $licence = $this->createElement("licence", "Distributed under a Creative Commons Attribution-ShareAlike 4.0 Unported License");
        $targetAttrib = $this->createAttribute('target');
        $targetAttrib->value = 'http://creativecommons.org/licenses/by-sa/4.0/';
        $licence->appendChild($targetAttrib);
        $availability->appendChild($licence);
        $date = $this->createElement("date",$this->currentDate);
        $pubStmt->appendChild($date);
        $p = $this->createElement("p", $this->headers["h20"] ?? "");
        $availability->appendChild($p);
        $pubStmt->appendChild($availability);
    }

    function setSourceDescription($fileDesc): void {
        $sourceDesc = $this->createElement("sourceDesc");
        $msDesc = $this->createElement("msDesc");
        $sourceDesc->appendChild($msDesc);
        $this->setMsIdentifier($msDesc);
        $this->setMsContents($msDesc);
        $this->setPhysicalDescription($msDesc);
        $this->setHistoryDescription($msDesc);
        $fileDesc->appendChild($sourceDesc);
    }

    /**
     * @param  $msDesc
     * @return array
     */
    function setMsIdentifier($msDesc): array {
        $msIdentifier = $this->createElement("msIdentifier");
        $settlement = $this->createElement("settlement", $this->headers["h17"] ?? "");
        $repository = $this->createElement("repository", $this->headers["h5"] ?? "");
        $idno = $this->createElement("idno", $this->headers["h8"] ?? "");
        $msIdentifier->appendChild($settlement);
        $msIdentifier->appendChild($repository);
        $msDesc->appendChild($msIdentifier);
        $msIdentifier->appendChild($idno);
        $this->setAltIdentifier($msIdentifier);
        return array($settlement, $idno);
    }

    /**
     * @param  $msDesc
     * @return array
     */
    function setMsContents($msDesc): array {
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
     * @param  $msDesc
     * @return array
     */
    function setPhysicalDescription($msDesc): array {
        $physDesc = $this->createElement("physDesc");
        $p = $this->createDocumentFragment();
        $catalogueEntry = $this->headers["h9"] ?? "";
        $p->appendXML('<p>For details see <ref target="' . $catalogueEntry . '">entry in database </ref></p>');
        $physDesc->appendChild($p);
        $msDesc->appendChild($physDesc);
        return array($physDesc, $p);
    }

    /**
     * @param  $msDesc
     */
    function setHistoryDescription($msDesc): void {
        $history = $this->createElement("history");
        $origin = $this->createElement("origin");
        $history->appendChild($origin);
        $p = $this->createDocumentFragment() ?? "";
        $dateOfOrigin = $this->headers["h3"] . '</origDate> from <origPlace>' . $this->headers["h18"];
        $p->appendXML('<p>Issued in <origDate>' . $dateOfOrigin . '</origPlace></p>');
        $origin->appendChild($p);
        $msDesc->appendChild($history);
    }

    /**
     * @param  $msIdentifier
     */
    function setAltIdentifier($msIdentifier): void {
        $altIdentifier = $this->createElement("altIdentifier");
        $typeAttrib = $this->createAttribute('type');
        $typeAttrib->value = $this->headers["h1"] ?? "";
        $altIdentifier->appendChild($typeAttrib);
        $settlement = $this->createElement("settlement", $this->headers["h10"] ?? "");
        $collection = $this->createElement("collection", $this->headers["h19"] ?? "");
        $idno = $this->createElement("idno", $this->headers["h7"] ?? "");
        $altIdentifier->appendChild($settlement);
        $altIdentifier->appendChild($collection);
        $msIdentifier->appendChild($altIdentifier);
        $altIdentifier->appendChild($idno);
    }
}
