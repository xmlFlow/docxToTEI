<?php namespace docx2tei\objectModel\body;

use docx2tei\objectModel\DataObject;

class Table extends DataObject {
    private $properties = array();
    private $rows = array();

    public function __construct(\DOMElement $domElement, $params) {
        parent::__construct($domElement, $params);
        $this->properties = $this->setProperties('w:tblPr/child::node()');
        $this->rows = $this->setContent('w:tr');
    }

    private function setContent(string $xpathExpression) {
        $content = array();
        $contentNodes = $this->getXpath()->query($xpathExpression, $this->getDomElement());
        if ($contentNodes->count() > 0) {
            foreach ($contentNodes as $contentNode) {
                $row = new Row($contentNode, $this->getParameters());
                $content[] = $row;
            }
        }
        return $content;
    }

    public function getContent() {
        return $this->rows;
    }
}
