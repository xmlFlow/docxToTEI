<?php

namespace docx2tei\tei;

use docx2tei\XMLUtils;
use DOMDocument;

class FinalDocument extends DOMDocument {
    var $document;

    public function __construct(TEIDocument $document) {
        parent::__construct('1.0', 'utf-8');
        $this->document = $document;

        // DOM operations
        XMLUtils::removeTagByName($this->document,"title");
        XMLUtils::removeTagByNameLeaveChildren($this->document,"p");


    }



}
