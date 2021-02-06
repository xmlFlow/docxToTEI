<?php
//This software is  Licensed under GPL 2. See License
namespace docx2tei\tei;

use DOMDocument;
use DOMElement;

class Facsimiles extends DOMDocument {
    var $document;

    public function __construct(TEIDocument $doc) {
        parent::__construct('1.0', 'utf-8');
        $this->document = $doc;
        $this->setFacsimiles();
    }

    function setFacsimiles(): void {
        $facsimiles = $this->document->xpath->query('//root/text/sec/title[text()="' . $this->document->cfg->sections->facsimiles . '"]/parent::sec/sec/title');
        $abs = $this->document->xpath->query('//title[starts-with(text(),"' . $this->document->cfg->sections->edition . '")]/parent::sec/sec/title');

        $lastPB = null;
        foreach ($facsimiles as $facsimile) {

            list($xml_id, $facs, $fpage) = explode(":", $facsimile->textContent);
            $surface = $this->createElement("surface");
            $idAttrib = $this->createAttribute('xml:id');
            $idAttrib->value = $xml_id;
            $surface->appendChild($idAttrib);
            $facsAttrib = $this->createAttribute('facs');
            $facsAttrib->value = $facs;
            $surface->appendChild($facsAttrib);
            $this->createCoordinates($surface);
            $pageFound = false;

            foreach ($abs as $ab) {
                list($abOrPb, $attrib, $page) = explode('@', $nodeValue = $ab->textContent);
                if ($pageFound==false && $abOrPb == "pb" && $xml_id == ltrim($attrib, '#') && $fpage == $page) {
                    $pageFound = true;
                }

                if($pageFound && $abOrPb == "ab") {
                    $zone = $this->createElement("zone");
                    $idAttrib = $this->createAttribute('xml:id');
                    $idAttrib->value = ltrim($attrib,'#');
                    $zone->appendChild($idAttrib);
                    $this->createCoordinates($zone);
                    $surface->appendChild($zone);
                }

                if ($pageFound==true && $abOrPb == "pb" && $xml_id != ltrim($attrib, '#') && $fpage != $page) {
                    $pageFound = false;
                }

            }
            $this->document->facsimile->appendChild($this->document->importNode($surface, true));

        }

    }

    /**
     * @param DOMElement $surface
     */
    function createCoordinates(DOMElement $surface): void {
        foreach (["ulx", "uly", "lrx", "lry"] as $attr) {
            $coord = $this->createAttribute($attr);
            $coord->value = 0;
            $surface->appendChild($coord);
        }
    }
}
