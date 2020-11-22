<?php

namespace docx2tei\tei;

use docx2tei\XMLUtils;
use DOMDocument;
use DOMElement;

class Edition extends DOMDocument {
    var $document;

    public function __construct(TEIDocument $document) {
        parent::__construct('1.0', 'utf-8');
        $this->document = $document;
        $edition = $this->document->xpath->query('//root/text/sec/title[starts-with(text(),"' . $this->document->cfg->sections->edition . '")]');
        if (count($edition) == 0) {
            XMLUtils::print_error("[Error] Edition section not found");
        } else {
            $div = $this->createDiv();
            $this->createSections($div);
            $divText = $div->ownerDocument->saveXML($div);
            $this->document->body->appendChild($this->document->importNode($div, true));
        }


    }

    private function createDiv() {
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
        $lang = $this->document->cfg->default_language;
        if (count($matches) == 2) {
            $lang = $matches[1];
        }
        $langAttr->value = $lang;
        $div->appendChild($langAttr);
        return $div;
    }

    /**
     * @param DOMElement $div
     */
    private function createSections(DOMElement $div): void {
        $sections = $this->document->xpath->query('//root/text/sec/title[starts-with(text(),"' . $this->document->cfg->sections->edition . '")]/parent::sec/sec');
        foreach ($sections as $section) {
            $ab = $this->createSectionBegin($section, $div);
            $contents = $this->document->xpath->query('child::node()', $section);
            if ($contents) {
                foreach ($contents as $content) {
                    $s = $content->ownerDocument->saveXML($content);
                    # ! order is important. never change order #
                    $s = XMLUtils::cleanMultipleSpaces($s);
                    $s = XMLUtils::createLineBegin($s);
                    $s = XMLUtils::createLineBeginNoBreak($s);
                    # no line breaks in text
                    $s = XMLUtils::joinLines($s);
                    # create gaps of illegible and lost characters
                    $s = XMLUtils::createGap($s, 'lost', '\/');
                    $s = XMLUtils::createGap($s, 'illegible', '\+');
                    # create spaces
                    $s = XMLUtils::createSpaces($s);
                    # structured content xy{content}
                    $s = XMLUtils::createStructuredContent($s);
                    # set . as <orig> dot
                    $s = XMLUtils::createDot($s);

                    #rename tags
                    $s = XMLUtils::tagReplace($s, 'p', 'ab');


                    # ! order is important. never change order #

                    $frag = $this->createDocumentFragment();
                    $frag->appendXML($s);
                    if (!is_null($ab)) {
                        $ab->appendChild($frag);
                    }
                }
            }

        }
    }

    /**
     * @param $section
     * @param DOMElement $div
     */
    private function createSectionBegin($section, DOMElement $div): ?DOMElement {
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
                    $typeAttr->value = $value1;
                    $ab->appendChild($typeAttr);
                    $facs = $this->createAttribute('facs');
                    $facs->value = $value2;
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
                XMLUtils::print_error("[Error] not enough information in " . $titleContent);
            }
        } else {
            XMLUtils::print_error("[Error]  In edition block, section header not defined ");
        }
        return $ab;
    }
}
