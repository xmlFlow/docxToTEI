<?php

namespace docx2tei\tei;

use docx2tei\XMLUtils;
use DOMDocument;

class FinalDocument extends DOMDocument {
    var $document;

    public function __construct(TEIDocument $document) {
        parent::__construct('1.0', 'utf-8');

        XMLUtils::removeTitleInBody($document, "title");
        //XMLUtils::removeElementsInTag($document, '//ab/p');
        XMLUtils::removeElementsInTag($document, '//add/w');
        XMLUtils::removeElementsInTag($document, '//w/w');
        XMLUtils::removeElementsInTag($document, '//orig/orig');
        XMLUtils::removeTags($document, "//bold");
        XMLUtils::removeTags($document, "//table-wrap");
        XMLUtils::addParagraphsBetweenAnonymousBlocks($document);
        XMLUtils::addChildElement($document, "ab", "lb");
        XMLUtils::enumerateLBs($document);

        // String operations

        $s = $document->saveXML();
        $s = XMLUtils::removeTagsWithoutContent($s);
        $this->isComplexStatementsCorrect($s);
        $s = XMLUtils::createComplexSentence($s);
        $s = XMLUtils::handleLineBreakNoWords($s);
        $s = XMLUtils::handleSurroundingAdd($s);
        $s = XMLUtils::createXMLTagsFromUncompatibleTags($s);

        ## Error messages
        preg_replace_callback_array(
            [
                '/\w+\s+{.*}|\s+\w+\{.*}/' => function ($match) {
                    XMLUtils::print_error("[Error] Formatting error: please correct " . $match[0]);
                },
                '/#[\w|?|]+(@(\w)*)*\{(<.*>(.)*<\/w>)\s\V*/U' => function ($match) {
                    XMLUtils::print_error("[Error] Formatting error of  following places: missing ending # in  " . $match[0]);
                },
                '/[\w|?|]+(@(\w)*)*\{(<.*>(.)*<\/w>)\s\V*/U' => function ($match) {
                    XMLUtils::print_error("[Error] Formatting error of  following places: missing ending # in  " . $match[0]);
                }
            ],
            $s
        );



// Create new Dom
        XMLUtils::printPHPErrors();
        XMLUtils::removeTags($document, "//add/w");

        $newDom = new DOMDocument();
        $newDom->loadXML($s);

        $this->document = $newDom;
    }

    function isComplexStatementsCorrect($s): bool {
        preg_match_all('/#SB/', $s, $SBS);
        preg_match_all('/#SE/', $s, $SES);
        $diff = count($SBS[0]) - count($SES[0]);
        if ($diff > 0) {
            XMLUtils::print_error("[Fatal Error] Your document contains " . $diff . " #SB elements, which has to be  enclosed with #SE. ", true);
        } else if ($diff < 0) {
            XMLUtils::print_error("[Fatal Error] Your document contains " . abs($diff) . " #SE elements, which has to be  begin with #SB", true);
        }
        return true;
    }

    public function getDocumentElement() {
        return $this->document;
    }
}
