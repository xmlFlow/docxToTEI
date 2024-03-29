<?php namespace docx2tei\objectModel\body;

use docx2tei\objectModel\DataObject;
use DOMElement;

class Cell extends DataObject {
    private $properties = array();
    private $paragraphs = array();
    private $colspan;
    private $rowspan;
    private $isMerged;
    private $cellNumber;

    public function __construct(DOMElement $domElement, $params, $cellNumber) {
        parent::__construct($domElement, $params);
        $this->cellNumber = $cellNumber;
        $this->isMerged = $this->defineMerged();
        $this->colspan = $this->extractColspanNumber();
        $this->extractRowspanNumber();
        $this->paragraphs = $this->setParagraphs();
        $this->properties = $this->setProperties('w:tcPr');
    }

    public function defineMerged(): bool {
        $mergeNodes = $this->getXpath()->query('w:tcPr/w:vMerge', $this->getDomElement());
        if ($mergeNodes->count() == 0) {
            return false;
        }
        return true;
    }

    private function extractColspanNumber(): int {
        $colspan = 1;
        $colspanAttr = $this->getXpath()->query('w:tcPr/w:gridSpan/@w:val', $this->getDomElement());
        if ($this->isOnlyChildNode($colspanAttr)) {
            $colspan = $colspanAttr[0]->nodeValue;
        }
        return $colspan;
    }

    private function extractRowspanNumber(): void {
        $rowMergedNode = $this->getXpath()->query('w:tcPr/w:vMerge[@w:val=\'restart\']', $this->getDomElement());
        $this->rowspan = 1;
        if ($rowMergedNode->count() > 0) {
            $this->extractRowspanRecursion($this->getDomElement());
        }
    }

    private function extractRowspanRecursion(DOMElement $node): void {
        $cellNodeListInNextRow = $this->getXpath()->query('parent::w:tr/following-sibling::w:tr[1]/w:tc', $node);
        $numberOfCells = 0; // counting number of cells in a row
        $mergedNode = null; // retrieving possibly merged cell node
        foreach ($cellNodeListInNextRow as $cellNodeInNextRow) {
            $colspanNode = $this->getXpath()->query('w:tcPr/w:gridSpan/@w:val', $cellNodeInNextRow);
            if ($colspanNode->count() == 0) {
                $numberOfCells++;
            } else {
                $numberOfCells += intval($colspanNode[0]->nodeValue) - 1;
            }
            if ($numberOfCells == $this->cellNumber) {
                $mergedNode = $cellNodeInNextRow;
                break;
            }
        }
// check if the node is actually merged
        if ($mergedNode) {
            $isActuallyMerged = $this->getXpath()->query('w:tcPr/w:vMerge', $mergedNode);
            if ($isActuallyMerged->count() > 0) {
                $this->rowspan++;
                $this->extractRowspanRecursion($mergedNode);
            }
        }
    }

    public function getContent(): array {
        return $this->paragraphs;
    }

    public function getColspan(): int {
        return $this->colspan;
    }

    public function getRowspan(): int {
        return $this->rowspan;
    }
}
