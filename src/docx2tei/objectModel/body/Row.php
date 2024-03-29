<?php namespace docx2tei\objectModel\body;

use docx2tei\objectModel\DataObject;
use DOMElement;

class Row extends DataObject {
    private $properties = array();
    private $cells = array();

    public function __construct(DOMElement $domElement, $params) {
        parent::__construct($domElement, $params);
        $this->properties = $this->setProperties('w:trPr/child::node()');
        $this->cells = $this->setContent('w:tc');
    }

    private function setContent(string $xpathExpression) {
        $content = array();
        $contentNodes = $this->getXpath()->query($xpathExpression, $this->getDomElement());
        if ($contentNodes->count() > 0) {
            foreach ($contentNodes as $contentNode) {
// calculating cell number
                $cellNumber = 1;
                $precedeSiblingNodes = $this->getXpath()->query('preceding-sibling::w:tc', $contentNode);
                foreach ($precedeSiblingNodes as $precedeSiblingNode) {
                    $colspan = $this->getXpath()->query('w:tcPr/w:gridSpan/@w:val', $precedeSiblingNode);
                    if ($colspan->count() == 0 || empty($colspan)) {
                        $cellNumber++;
                    } else {
                        $cellNumber += intval($colspan[0]->nodeValue);
                    }
                }
                // Omit merged nodes
                $colspansMerged = $this->getXpath()->query('w:tcPr/w:vMerge[@w:val="continue"]', $contentNode);
                if (!$colspansMerged->count() > 0) {
                    $cell = new Cell($contentNode, $this->getParameters(), $cellNumber);
                    $content[] = $cell;
                }
            }
        }
        return $content;
    }

    public function getContent() {
        return $this->cells;
    }
}
