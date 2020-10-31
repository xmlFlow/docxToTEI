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
            foreach ($sections as $section) {
                $title = $this->document->xpath->query('./title',$section)->item(0);
                if($title) {
                    $titleContent = $section->ownerDocument->saveXML($title);;
                    # Clean xml tags
                    $titleContent = preg_replace('/<(\/)*title>/', '', $titleContent);
                    $titleAttribs = explode("@", $titleContent);
                    list ($type, $value1, $value2) = $titleAttribs;
                    $ab = null;
                    $extraAttributes  = array_slice($titleAttribs,3);
                    if ($type="pb") {
                        //<pb n="1r" facs="#surface1"/>
                        $ab = $this->createElement("pb");
                        $typeAttr = $this->createAttribute('n');
                        $typeAttr->value = $value1;
                        $ab->appendChild($typeAttr);
                        $facs = $this->createAttribute('facs');
                        $facs->value = $value2;
                        $ab->appendChild($facs);
                    }
                    elseif ($type="ab") {
                        $ab = $this->createElement("ab");
                        $facsAttr = $this->createAttribute('facs');
                        $facsAttr->value = $value1;
                        $n = $this->createAttribute('n');
                        $n->value = $value2;
                        $ab->appendChild($n);
                        $ab->appendChild($facsAttr);

                    }
                    else {
                        $this->print_error("[Error]  Edition blocks should be define as  ab or pb");
                    }

                    $x = 5;
                }
                else {
                    $this->print_error("[Error]  In edition block, section header not defined ");
                }
            }



        }
        $tmp = $this->document->structuredDocument->saveXML();
        $y=1;


    }
    private  function norm(string $s) {

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