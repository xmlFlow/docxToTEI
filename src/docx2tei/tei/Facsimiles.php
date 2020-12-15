<?php

namespace docx2tei\tei;

use docx2tei\XMLUtils;
use DOMDocument;
use DOMElement;

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
            XMLUtils::print_error("[Error] No facsimiles " . $this->document->cfg->sections->facsimiles);
        } else {
            foreach ($facsimiles as $facsimile) {
                if ($facsimile->tagName !== "title") {
                    XMLUtils::print_error("[Error] facsimile not formatted properly. Use  Heading 2 format in Word " . $facsimile->tagName);
                }
                $text = (string)$facsimile->textContent;
                if (strlen($text) == 0) {
                    XMLUtils::print_error("[Error]  Content of facsimile is not defined or Formatting error");
                } else {
                    $surfaceParts = explode(":", $text);
                    if (count($surfaceParts) < 3) {
                        XMLUtils::print_error("[Error]  Surface formatting error. Should be e.g. in surface1: E_12.png:1r " . $surfaceParts);
                    } else {
                        list($xml_id, $facs, $page) = $surfaceParts;
                        $surface = $this->createElement("surface");
                        $idAttrib = $this->createAttribute('xml:id');
                        $idAttrib->value = $xml_id;
                        $surface->appendChild($idAttrib);
                        $facsAttrib = $this->createAttribute('facs');
                        $facsAttrib->value = $facs;
                        $surface->appendChild($facsAttrib);
                        $this->createCoordinates($surface);
                        $abs = $this->document->xpath->query('//root/text/sec/title[starts-with(text(),"' . $this->document->cfg->sections->edition . '")]/parent::sec/sec');

                        foreach ($abs as $ab) {
                            $abElement = $this->document->xpath->query("title", $ab);
                            if (count($abElement) > 0) {
                                $nodeValue = $abElement->item(0)->nodeValue;
                                if (substr($nodeValue, 0, 2) === "ab") {
                                    $parts = explode('@', $nodeValue);
                                    if (count($parts) > 1) {
                                        $zone = $this->createElement("zone");
                                        $idAttrib = $this->createAttribute('xml:id');
                                        $idAttrib->value = str_replace("#", "", $parts[1]);
                                        $zone->appendChild($idAttrib);
                                        $this->createCoordinates($zone);
                                        $surface->appendChild($zone);
                                    }
                                }

                            }

                        }

                        $this->document->facsimile->appendChild($this->document->importNode($surface, true));
                    }
                }
            }
        }
    }

    /**
     * @param DOMElement $surface
     */
    private function createCoordinates(DOMElement $surface): void {
        foreach (["ulx", "uly", "lrx", "lry"] as $attr) {
            $coord = $this->createAttribute($attr);
            $coord->value = 0;
            $surface->appendChild($coord);
        }
    }
}
