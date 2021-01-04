<?php

namespace docx2tei\tei;

use docx2tei\XMLUtils;
use DOMDocument;

class EnglishTranslation extends DOMDocument {
    var $document;

    public function __construct(TEIDocument $document) {
        parent::__construct('1.0', 'utf-8');
        $this->document = $document;
        $this->setEnglishTranslation();
    }

    protected function setEnglishTranslation(): void {
        $etSec = $this->document->xpath->query('//root/text/sec/title[text()="' . $this->document->cfg->sections->et . '"]/parent::sec/child::node()');
        if (count($etSec) == 0) {
            XMLUtils::print_error("[Warning] English Translation section not defined");
        } else {
            $div = $this->createElement("div");
            $idAttrib = $this->createAttribute('xml:id');
            $idAttrib->value = "et";
            $div->appendChild($idAttrib);
            $typeAttr = $this->createAttribute('type');
            $typeAttr->value = "english_translation";
            $div->appendChild($typeAttr);
            $correspAttr = $this->createAttribute('corresp');
            $correspAttr->value = "#corresp";
            $div->appendChild($correspAttr);
            $langAttr = $this->createAttribute('xml:lang');
            $langAttr->value = "eng";
            $div->appendChild($langAttr);

            foreach ($etSec as $et) {
                if (strlen($et->textContent) > 0) {
                    $pbs = $this->createSectionBegin($et, $div);
                    $s = $et->ownerDocument->saveXML($et);
                    $s = XMLUtils::getMarkups($s);
                    # no line breaks in text
                    $s = XMLUtils::joinLines($s);
                    # create spaces
                    # structured content xy{content}
                    $ab = $this->createDocumentFragment();
                    $ab->appendXML($s);
                    $div->appendChild($ab);
                }
            }
            $this->document->body->appendChild($this->document->importNode($div, true));
        }
    }

    /**
     * @param $section
     * @param DOMElement $div
     */
    private function createSectionBegin($section, $div) {
        $ab = null;
        $type = "";
        $title = $this->document->xpath->query('./title', $section)->item(0);
        if ($title) {
            $titleContent = $section->ownerDocument->saveXML($title);
            # Clean xml tags
            $titleContent = preg_replace('/<(\/)*title>/', '', $titleContent);
            $titleAttribs = explode("@", $titleContent);
            if (count($titleAttribs) >= 2) {
                list ($type, $value1) = $titleAttribs;
                $type = trim(strtolower($type));
                if ($type == "pb") {
                    //<pb n="1r" facs="#surface1"/>
                    $ab = $this->createElement("pb");
                    $typeAttr = $this->createAttribute('n');
                    $typeAttr->value = $value1;
                    $ab->appendChild($typeAttr);
                } else {
                    XMLUtils::print_error("[Error]  Wrong type in edition: " . $type);
                }
                $div->appendChild($ab);
            } else {
                XMLUtils::print_error("[Error] Text should be either correctly formatted: title in H1  or should be text. Check formatting {" . $titleContent . " }");
            }
        }
        return $ab;
    }
}
