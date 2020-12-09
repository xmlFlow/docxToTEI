<?php

namespace docx2tei\tei;

use docx2tei\XMLUtils;
use DOMDocument;

class FinalDocument extends DOMDocument {
    var $document;

    public function __construct(TEIDocument $document) {
        parent::__construct('1.0', 'utf-8');

        // DOM operations
        XMLUtils::removeTitleInBody($document, "title");
        XMLUtils::removeParagraphsInBody($document);
        XMLUtils::addChildElement($document, "ab", "lb");
        XMLUtils::removeControlledVocabsWordTagging($document);
        XMLUtils::enumerateLineBeginings($document);
        XMLUtils::removeTags($document, "//bold");
        XMLUtils::removeTags($document, "//table-wrap");
        XMLUtils::addParagraphsBetweenAnonymousBlocks($document);


        // String operations
        $s = $document->saveXML();
        $s = XMLUtils::createFootnoteTags($s);
        $s = XMLUtils::createNotesWithCorrectTags($s);
        $s = XMLUtils::removeTagsWithoutContent($s);
        # Complex  sentence
        $this->isComplexStatementsCorrect($s);
        $s = XMLUtils::createComplexSentence($s);
        $s = XMLUtils::handleLastMinus($s);

        ## Error messages
        preg_match_all('/\w+\s+{.*}|\s+\w+\{.*}|#\w+\{.*}/i', $s, $matches);
        if (count($matches[0]) > 0) {
            foreach ($matches as $match) {
                XMLUtils::print_error("[Error] Formatting error, please correct " . $match[0]);
                XMLUtils::print_error("[Error] Possible reasons : Unknown Tag '#tag{}#'. Empty spaces  '' between tags. Hashtag '#' missing, Brackets '{}' missing ",true);
            }
        }


        // Create new Dom
        $newDom = new DOMDocument();
        XMLUtils::printPHPErrors();
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
