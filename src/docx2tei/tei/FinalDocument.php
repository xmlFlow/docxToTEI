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
        $s = XMLUtils::removeEmptyTags($s);

        // Create new Dom
        $newDom = new DOMDocument();
        XMLUtils::printPHPErrors();
        $newDom->loadXML($s);

        $this->document = $newDom;

    }


    public function getDocumentElement() {
        return $this->document;
    }


}
