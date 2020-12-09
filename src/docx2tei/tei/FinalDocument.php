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
        XMLUtils::addChildElement($document, "ab","lb");
        XMLUtils::removeControlledVocabsWordTagging($document);
        XMLUtils::enumerateLineBeginings($document);
        XMLUtils::removeTags($document,"//bold");
        XMLUtils::removeTags($document,"//table-wrap");
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


        // Create new Dom
        $newDom = new DOMDocument();
        XMLUtils::printPHPErrors();
        $newDom->loadXML($s);

        $this->document = $newDom;

    }

    function isComplexStatementsCorrect($s): bool {
        preg_match_all('/#SB/i', $s, $SBS);
        preg_match_all('/#SE/i', $s, $SES);

        $diff = count($SBS[0]) - count($SES[0]);
        if ($diff > 0) {
            XMLUtils::print_error("[Fatal Error] Your document contains " . $diff . " #SB elements, which has to be  enclosed with #SE. ");
            exit('[Fatal Error] Please correct your Word file  and upload again');

        } else if ($diff < 0) {
            XMLUtils::print_error("[Fatal Error] Your document contains " . abs($diff) . " #SE elements, which has to be  begin with #SB");
            exit('[Fatal Error] Please your Word file  and upload again');
        }

        return true;
    }
    public function getDocumentElement() {
        return $this->document;
    }


}
