<?php

namespace docx2tei\tei;

use docx2tei\XMLUtils;
use DOMDocument;

class Commentary extends DOMDocument {
    var $document;

    public function __construct(TEIDocument $document) {
        parent::__construct('1.0', 'utf-8');
        $this->document = $document;
        $this->getCommentry();
    }

    protected function getCommentry(): void {
        $etSec = $this->document->xpath->query('//root/text/sec/title[text()="' . $this->document->cfg->sections->commentary . '"]/parent::sec/child::node()');
        if (count($etSec) == 0) {
            XMLUtils::print_error("[Error] Commentary section not defined");
        } else {
            $div = $this->createElement("div");
            $idAttrib = $this->createAttribute('xml:id');
            $idAttrib->value = "commentary";
            $div->appendChild($idAttrib);
            $typeAttr = $this->createAttribute('type');
            $typeAttr->value = "commentary";
            $div->appendChild($typeAttr);
            $langAttr = $this->createAttribute('xml:lang');
            $langAttr->value = "eng";
            $div->appendChild($langAttr);
            foreach ($etSec as $et) {
                if (strlen($et->textContent) > 0) {
                    $s = $et->ownerDocument->saveXML($et);
                    $s = XMLUtils::cleanMultipleSpaces($s);
                    $s = XMLUtils::createFootnoteTags($s);
                    $s = XMLUtils::createStructuredContent($s);
                    $ab = $this->createDocumentFragment();
                    $ab->appendXML($s);
                    $div->appendChild($ab);
                }
            }
            $this->document->body->appendChild($this->document->importNode($div, true));
        }
    }

}
