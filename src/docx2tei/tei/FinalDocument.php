<?php

namespace docx2tei\tei;

use docx2tei\XMLUtils;
use DOMDocument;

class FinalDocument extends DOMDocument {
    var $document;

    public function __construct(TEIDocument $document) {
        parent::__construct('1.0', 'utf-8');
        $this->document = $document;
        $this->cleanTitle();
    }

    protected function cleanTitle(): void {
        //$titles = $this->document->xpath->query('//root/text/sec/title');

            $placesNodes = $this->document->getElementsByTagName('title');
            while ($placesNodes->length > 0) {
                $node = $placesNodes->item(0);
                $node->parentNode->removeChild($node);
            }

    }

}
