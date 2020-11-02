<?php


namespace docx2tei\tei;


use DOMDocument;

class Facsimiles extends DOMDocument {

    var $document;

    public function __construct(TEIDocument $document) {
        parent::__construct('1.0', 'utf-8');
        $this->document = $document;
        $this->setFacsimiles();
    }

    function setFacsimiles(): void {
        $facsimiles = $this->document->xpath->query('//root/text/sec/title[text()="' . $this->document->cfg->sections->facsimiles . '"]/parent::sec/sec/title');
        if (count($facsimiles) == 0) {
            $this->document->print_error("[Error] No facsimiles defined");
        } else {
            foreach ($facsimiles as $facsimile) {
                if ($facsimile->tagName !== "title") {
                    $this->document->print_error("[Error] facsimile not formatted properly. Use  Heading 2 format in Word ");
                }
                $text = (string)$facsimile->textContent;
                if (strlen($text) == 0) {
                    $this->document->print_error("[Error]  Content of facsimile is not defined or Formatting error");
                } else {
                    $surfaceParts = explode(":", $text);
                    if (count($surfaceParts) < 3) {
                        $this->document->print_error("[Error]  Surface formatting error. Should be e.g. in surface1: E_12.png:1r");
                    } else {
                        list($xml_id, $facs, $page) = $surfaceParts;
                        $facsimile = $this->createElement("facsimile");
                        $surface = $this->createElement("surface");
                        $idAttrib = $this->createAttribute('xml:id');
                        $idAttrib->value = $xml_id;
                        $surface->appendChild($idAttrib);
                        $facsAttrib = $this->createAttribute('facs');
                        $facsAttrib->value = $facs;
                        $surface->appendChild($facsAttrib);
                        foreach (["ulx", "uly", "lrx", "lry"] as $attr) {
                            $coord = $this->createAttribute($attr);
                            $coord->value = 0;
                            $surface->appendChild($coord);
                        }

                        $facsimile->appendChild($surface);
                        $this->document->teiHeader->appendChild($this->document->importNode($facsimile, true));
                    }
                }


            }
        }
    }

}