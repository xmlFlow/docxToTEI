<?php


namespace docx2tei\tei;


class Abstracts extends \DOMDocument {
    var $document;

    public function __construct(TEIDocument  $document) {
        parent::__construct('1.0', 'utf-8');
        $this->document = $document;
        $this->setAbstract();
    }


    protected function setAbstract(): void {
        $abstractSec = $this->document->xpath->query('//root/text/sec/title[text()="' . $this->document->cfg->sections->abstract . '"]/parent::sec/p');
        if (count($abstractSec) == 0) {
            $this->print_error("[Error] Abstract text not defined");
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
                    $content = $abstract->ownerDocument->saveXML($abstract);

                    // replace all <p>s to <ab> s and multiple whitespaces
                    $content = preg_replace('/<p>/i', '<ab>', $content);
                    $content = preg_replace('/<\/p>/i', '</ab>', $content);
                    $content = preg_replace('/\s+/i', ' ', $content);
                    $ab = $this->createDocumentFragment();
                    $ab->appendXML($content);
                    $div->appendChild($ab);
                }
            }
            $this->document->body->appendChild($this->document->importNode($div, true));
        }
    }
}