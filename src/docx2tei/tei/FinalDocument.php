<?php

namespace docx2tei\tei;

use docx2tei\XMLUtils;
use DOMDocument;

class FinalDocument extends DOMDocument {
    var $document;

    public function __construct(TEIDocument $doc) {
        parent::__construct('1.0', 'utf-8');


        # handle choice elements specially
        XMLUtils::removeElementName($doc, '//choice/*/w');

        XMLUtils::removeElementName($doc, '//ab/p');
        XMLUtils::removeTitleInBody($doc, "title");
        XMLUtils::removeElementName($doc, "//bold");
        XMLUtils::removeElementName($doc, "//table-wrap");
        XMLUtils::addParagraphsBetweenAnonymousBlocks($doc);

        # LBs  adds begin element, enumerate, then remove the last lb.
        XMLUtils::addChildElement($doc, "ab", "lb");
        XMLUtils::removeLastElementOfParent($doc, 'lb');
        XMLUtils::removeElementBefore($doc, 'table', 'lb');
        XMLUtils::enumerateLBs($doc);

        XMLUtils::removeElementName($doc, "//w/w");
        XMLUtils::removeElementName($doc, "//w/*/w");
        XMLUtils::removeElementName($doc, "//sec");
        XMLUtils::removeElementName($doc, "//orig/orig");

        // String operations

        $s = $doc->saveXML();


        $s = XMLUtils::getMarkups($s);
        //removeTagsWithoutContent
        $s = preg_replace('/<\w+>\s*<\/\w+>/i', ' ', $s);
        $this->isComplexStatementsCorrect($s);
        // createComplexSentence <s>
        $s = preg_replace_callback_array([
            '/#SB(.|\n)*?#SE/' => function ($matches) {
                $s = $matches[0];
                $s = preg_replace('/#SB@([a-z]{3})@([IMF])/', '<s xml:lang="$1" part="$2">', $s);
                $s = preg_replace('/#SB@([a-z]{3})/', '<s xml:lang="$1">', $s);
                $s = preg_replace('/#SB@([IMF])/', '<s part="$1">', $s);
                $s = preg_replace('/#SB/', '<s>', $s);
                $s = preg_replace('/#SE/', '</s>', $s);
                return $s;
            }
        ], $s);

        $s = XMLUtils::handleLineBreakNoWords($s);
        $s = XMLUtils::createXMLTagsFromUncompatibleTags($s);
        $s = XMLUtils::createSurroundWordForChoice($s);
        $s = XMLUtils::createDot($s);


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
