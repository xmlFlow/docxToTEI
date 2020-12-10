<?php
namespace docx2tei\tei;
use docx2tei\XMLUtils;
use DOMDocument;
class Synopsis extends DOMDocument {
    var $document;
public function __construct(TEIDocument $document) {
        parent::__construct('1.0', 'utf-8');
        $this->document = $document;
        $this->setEnglishTranslation();
    }
protected function setEnglishTranslation(): void {
        $etSec = $this->document->xpath->query('//root/text/sec/title[text()="' . $this->document->cfg->sections->synopsis . '"]/parent::sec/child::node()');
        if (count($etSec) == 0) {
            XMLUtils::print_error("[Warning] synopsis section not defined");
        } else {
            $div = $this->createElement("div");
            $idAttrib = $this->createAttribute('xml:id');
            $idAttrib->value = "synopsis";
            $div->appendChild($idAttrib);
            $typeAttr = $this->createAttribute('type');
            $typeAttr->value = "synopsis";
            $div->appendChild($typeAttr);
            $langAttr = $this->createAttribute('xml:lang');
            $langAttr->value = "eng";
            $div->appendChild($langAttr);
            foreach ($etSec as $et) {
                if (strlen($et->textContent) > 0) {
                    $s = $et->ownerDocument->saveXML($et);
                    $s = XMLUtils::removeMultipleSpacesandZWNJS($s);
                    $s = XMLUtils::createFootnoteTags($s);
                    $s = XMLUtils::createStructuredContent($s);
                    # create spaces
                    $s = XMLUtils::createSpaceTag($s,'\.');
                    $ab = $this->createDocumentFragment();
                    $ab->appendXML($s);
                    $div->appendChild($ab);
                }
            }
            $this->document->body->appendChild($this->document->importNode($div, true));
        }
    }
}
