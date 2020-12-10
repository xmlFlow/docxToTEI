<?php namespace docx2tei\objectModel;
use docx2tei\objectModel\body\Par;
use DOMElement;
use DOMNodeList;
use DOMXPath;
abstract class DataObject {
    private $domElement;
    private $xpath;
    private $flatSectionId;
    private $dimensionalSectionId = array();
    private $params;
public function __construct(DOMElement $domElement, $params) {
        $this->domElement = $domElement;
        $this->xpath = Document::$xpath;
        $this->params = $params;
    }
public function getFlatSectionId(): int {
        return $this->flatSectionId;
    }
public function setFlatSectionId($flatSectionId): void {
        $this->flatSectionId = intval($flatSectionId);
    }
public function getDimensionalSectionId(): array {
        return $this->dimensionalSectionId;
    }
public function setDimensionalSectionId(array $dimensionalSectionId): void {
        $this->dimensionalSectionId = array_filter($dimensionalSectionId);
    }
public function getFirstElementByXpath(string $xpath, DOMElement $parentElement = null): ?DOMElement {
        $element = null;
        if ($parentElement) {
            $element = $this->getXpath()->query($xpath, $parentElement)[0];
        } else {
            $element = $this->getXpath()->query($xpath)[0];
        }
        return $element;
    }
protected function getXpath(): DOMXPath {
        return $this->xpath;
    }
protected function setProperties(string $xpathExpression): array {
        $styleNodes = $this->getXpath()->evaluate($xpathExpression, $this->domElement);
        $properties = $this->extractPropertyRecursion($styleNodes);
        return $properties;
    }
private function extractPropertyRecursion($styleNodes): array {
        $properties = array();
        foreach ($styleNodes as $styleNode) {
            if ($styleNode->hasAttributes()) {
                foreach ($styleNode->attributes as $attr) {
                    $properties[$styleNode->nodeName][$attr->nodeName] = $attr->nodeValue;
                }
            } elseif ($styleNode->hasChildNodes()) {
                $children = $this->getXpath()->query('child::node()', $styleNode);
                $rPr = $this->extractPropertyRecursion($children);
                $properties[$styleNode->nodeName] = $rPr;
            }
        }
        return $properties;
    }
protected function isOnlyChildNode(DOMNodeList $domNodeList): bool {
        if ($domNodeList->count() === 1) {
            return true;
        }
        return false;
    }
protected function setParagraphs(): array {
        $content = array();
        $parNodes = $this->getXpath()->query('w:p', $this->getDomElement());
        foreach ($parNodes as $parNode) {
            $par = new Par($parNode, $this->getParameters());
            $content[] = $par;
        }
        return $content;
    }
protected function getDomElement(): DOMElement {
        return $this->domElement;
    }
public function getParameters() {
        return $this->params;
    }
}
