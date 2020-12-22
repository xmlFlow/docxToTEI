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
            XMLUtils::print_error("[Warning] English Translation section not defined");
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
                    $pbs = $this->createSectionBegin($et, $div);
                    $s = $et->ownerDocument->saveXML($et);
                    $s = XMLUtils::getMarkups($s);
                    # no line breaks in text
                    $s = XMLUtils::joinLines($s);
                    # create spaces
                    # structured content xy{content}
                    $ab = $this->createDocumentFragment();
                    $ab->appendXML($s);
                    $div->appendChild($ab);
                }
            }
            $this->document->body->appendChild($this->document->importNode($div, true));
        }
    }

    /**
     * @param $section
     * @param DOMElement $div
     */
    private function createSectionBegin($section, $div): ?DOMElement {
        $ab = null;
        $type = "";
        $title = $this->document->xpath->query('./title', $section)->item(0);
        if ($title) {
            $titleContent = $section->ownerDocument->saveXML($title);
            # Clean xml tags
            $titleContent = preg_replace('/<(\/)*title>/', '', $titleContent);
            $titleAttribs = explode("@", $titleContent);
            if (count($titleAttribs) >= 3) {
                list ($type, $value1, $value2) = $titleAttribs;
                $type = trim(strtolower($type));
                if ($type == "pb") {
                    //<pb n="1r" facs="#surface1"/>
                    $ab = $this->createElement("pb");
                    $typeAttr = $this->createAttribute('n');
                    $typeAttr->value = $value2;
                    $ab->appendChild($typeAttr);
                    $facs = $this->createAttribute('facs');
                    $facs->value = $value1;
                    $ab->appendChild($facs);
                } elseif ($type == "ab") {
                    //<ab type="invocatio" corresp="#invocatio"/>
                    $ab = $this->createElement("ab");
                    $facsAttr = $this->createAttribute('type');
                    $facsAttr->value = $value2;
                    $n = $this->createAttribute('corresp');
                    $n->value = $value1;
                    $ab->appendChild($facsAttr);
                    $ab->appendChild($n);
                } else {
                    XMLUtils::print_error("[Error]  Wrong type in edition: " . $type);
                }
                foreach ($titleAttribs as $attribute) {
                    if (strpos($attribute, "=") > 0) {
                        $parts = explode('=', $attribute);
                        if (count($parts) == 2) {
                            $extraAttr = $this->createAttribute($parts[0]);
                            $extraAttr->value = $parts[1];
                            $ab->appendChild($extraAttr);
                        }
                    }
                }
                $div->appendChild($ab);
            } else {
                XMLUtils::print_error("[Error] Text should be either correctly formatted. title in H1  or should be text. Check formatting {" . $titleContent . " }");
            }
        }
        return $ab;
    }
}
