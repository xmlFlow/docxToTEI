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
            XMLUtils::print_error("[Error] English Translation section not defined");
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
                    $s = $et->ownerDocument->saveXML($et);
                    $s = XMLUtils::cleanMultipleSpaces($s);
                    $s = XMLUtils::createLineBeginNoBreak($s);
                    # no line breaks in text
                    $s = XMLUtils::joinLines($s);
                    # create gaps of illegible and lost characters
                    $s = XMLUtils::createGap($s, 'lost', '\/');
                    $s = XMLUtils::createGap($s, 'illegible', '\+');
                    # create spaces
                    $s = XMLUtils::createSpaces($s,'\.');
                    # structured content xy{content}
                    $s = XMLUtils::createStructuredContent($s);

                    $s = XMLUtils::createFootnoteTags($s);

                    $ab = $this->createDocumentFragment();
                    $ab->appendXML($s);
                    $div->appendChild($ab);
                }
            }
            $this->document->body->appendChild($this->document->importNode($div, true));
        }
    }

}
