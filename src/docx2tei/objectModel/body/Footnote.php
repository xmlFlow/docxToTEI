<?php namespace docx2tei\objectModel\body;

use docx2tei\objectModel\DataObject;
use docx2tei\objectModel\Document;

class Footnote extends DataObject {
    private $footnote;

    public function __construct(\DOMElement $domElement, $params) {
        parent::__construct($domElement, $params);
        $this->footnote = $this->extractFootnote();
    }

    private function extractFootnote(): ?string {
      return "";

    }

    public function getFootnote(): ?string {
        return $this->footnote;
    }


}
