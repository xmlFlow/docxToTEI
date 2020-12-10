<?php

namespace docx2tei\tei;

use docx2tei\XMLUtils;
use DOMDocument;

class Abstracts extends DOMDocument {
    var $document;

    public function __construct(TEIDocument $document) {
        parent::__construct('1.0', 'utf-8');
        $this->document = $document;
        $this->setAbstract();
    }

    protected function setAbstract(): void {
        $abstractSec = $this->document->xpath->query('//root/text/sec/title[text()="' . $this->document->cfg->sections->abstract . '"]/parent::sec/p');
        if (count($abstractSec) == 0) {
            XMLUtils::print_error("[Warning] Abstract section not defined");
        } else {
            $div = $this->createElement("div");
            $idAttrib = $this->createAttribute('xml:id');
            $idAttrib->value = "abs";
            $div->appendChild($idAttrib);
            $typeAttr = $this->createAttribute('type');
            $typeAttr->value = "abstract";
            $div->appendChild($typeAttr);
            $langAttr = $this->createAttribute('xml:lang');
            $langAttr->value = "eng";
            $div->appendChild($langAttr);
            foreach ($abstractSec as $abstract) {
                if (strlen($abstract->textContent) > 0) {
                    $s = $abstract->ownerDocument->saveXML($abstract);
                    $s = XMLUtils::removeMultipleSpacesandZWNJS($s);
                    $ab = $this->createDocumentFragment();
                    $ab->appendXML($s);
                    $div->appendChild($ab);
                }
            }
            $this->document->body->appendChild($this->document->importNode($div, true));
        }
    }
}
