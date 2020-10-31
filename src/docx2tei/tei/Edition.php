<?php


namespace docx2tei\tei;


class Edition extends \DOMDocument {
    var $document;

    public function __construct(TEIDocument  $document) {
        parent::__construct('1.0', 'utf-8');
        $this->document = $document;

        $edition = $this->document->xpath->query('//root/text/sec/title[starts-with(text(),"' . $this->document->cfg->sections->edition . '")]');

        if (count($edition)==0){
            $this->print_error("[Error]  Edition section not found");
        }
        else {

            #<div xml:id="ed" type="edition" xml:lang="nep">
            $this->createDiv();
            $sections= $this->document->xpath->query('//root/text/sec/title[starts-with(text(),"' . $this->document->cfg->sections->edition . '")]/parent::sec/sec');




        }
        $tmp = $this->document->structuredDocument->saveXML();
        $y=1;


    }

    private function createDiv(): void {
        $div = $this->createElement("div");
        $idAttrib = $this->createAttribute('xml:id');
        $idAttrib->value = "ed";
        $div->appendChild($idAttrib);
        $typeAttr = $this->createAttribute('type');
        $typeAttr->value = "edition";
        $div->appendChild($typeAttr);
        $langAttr = $this->createAttribute('xml:lang');
        $ed_lang = $this->document->xpath->query('//root/text/sec/title[starts-with(text(),"' . $this->document->cfg->sections->edition . '")]/parent::sec/title');
        $content = $ed_lang[0]->ownerDocument->saveXML($ed_lang[0]);
        preg_match('/\((.*?)\)/', $content, $matches);
        $lang = "nep";
        if (count($matches) == 2) {
            $lang = $matches[1];
        }

        $langAttr->value = $lang;
        $div->appendChild($langAttr);
        $this->document->body->appendChild($this->document->importNode($div, true));
    }
}