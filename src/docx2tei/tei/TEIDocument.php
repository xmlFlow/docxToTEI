<?php namespace docx2tei\tei;


use docx2tei\structure\Document;

class TEIDocument {
    var $structuredDocument;

    public function __construct(Document $structuredDocument) {

        $this->structuredDocument = $structuredDocument;

    }

    public function getDocument(string $pathToFile) {
        $this->structuredDocument->save($pathToFile);
        }

}