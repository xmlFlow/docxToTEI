<?php namespace docx2tei\objectModel;

use docx2tei\objectModel\body\Image;
use docx2tei\objectModel\body\Par;
use docx2tei\objectModel\body\Table;
use DOMXPath;

class Document {
    const SECT_NESTED_LEVEL_LIMIT = 5; // limit the number of possible levels for sections
// Represent styling for OOXMl structure elements
    const DOCX_STYLES_PARAGRAPH = "paragraph";
    const DOCX_STYLES_CHARACTER = "character";
    const DOCX_STYLES_NUMBERING = "numbering";
    const DOCX_STYLES_TABLE = "table";
    static $xpath;
    static $relationshipsXpath;
    static $stylesXpath;
    static $numberingXpath;
    static $footnotesXpath;
    private static $minimalHeadingLevel;
    private $content;
    private $relationships;
    private $styles;
    private $numbering;
    private $footnotes;

    public function __construct(array $params) {
        if (array_key_exists("relationships", $params)) {
            $this->relationships = $params["relationships"];
            self::$relationshipsXpath = new DOMXPath($this->relationships);
        }
        if (array_key_exists("styles", $params)) {
            $this->styles = $params["styles"];
            self::$stylesXpath = new DOMXPath($this->styles);
        }
        if (array_key_exists("numbering", $params)) {
            $this->numbering = $params["numbering"];
            self::$numberingXpath = new DOMXPath($this->numbering);
        }
        if (array_key_exists("footnotes", $params)) {
            $this->footnotes = $params["footnotes"];
            self::$footnotesXpath = new DOMXPath($this->footnotes);
        }
        self::$xpath = new DOMXPath($params["ooxmlDocument"]);
        $childNodes = self::$xpath->query("//w:body/child::node()");
        $content = array();
        foreach ($childNodes as $childNode) {
            switch ($childNode->nodeName) {
                case "w:p":
                    // There can be multiple drawings inside a run and multiple elements inside a drawing
                    if ($this->isDrawing($childNode)) {
                        // TODO add support for other drawings type, e.g., c:chart
                        self::$xpath->registerNamespace("pic", "http://schemas.openxmlformats.org/drawingml/2006/picture");
                        $imageNodes = self::$xpath->query(".//pic:pic", $childNode);
                        if ($imageNodes->length > 0) {
                            foreach ($imageNodes as $imageNode) {
                                $figure = new Image($imageNode, $params);
                                $content[] = $figure;
                            }
                        }
                    } else {
                        $par = new Par($childNode, $params);
                        $content[] = $par;
                    }
                    break;
                case "w:tbl":
                    $table = new Table($childNode, $params);
                    $content[] = $table;
                    break;
            }
        }
        $this->content = $this->addSectionMarks($content);
        self::$minimalHeadingLevel = $this->minimalHeadingLevel();
    }

    private function isDrawing($childNode): bool {
        $element = Document::$xpath->query("w:r//w:drawing", $childNode)[0];
        if ($element) return true;
        return false;
    }

    private function addSectionMarks(array $content): array {
        $flatSectionId = 0; // simple section id
        $dimensions = array_fill(0, self::SECT_NESTED_LEVEL_LIMIT, 0); // contains dimensional section id
        foreach ($content as $key => $object) {
            if (get_class($object) === "docx2tei\objectModel\body\Par" && $object->getType() && $object->getHeadingLevel()) {
                $flatSectionId++;
                $dimensions = $this->extractSectionDimension($object, $dimensions);
            }
            $object->setDimensionalSectionId($dimensions);
            $object->setFlatSectionId($flatSectionId);
        }
        return $content;
    }

    private function minimalHeadingLevel(): int {
        $minimalNumber = 7;
        foreach ($this->content as $dataObject) {
            if (get_class($dataObject) === "docx2tei\objectModel\body\Par" && in_array(Par::DOCX_PAR_LIST, $dataObject->getType())) {
                $number = $dataObject->getHeadingLevel();
                if ($number && $number < $minimalNumber) {
                    $minimalNumber = $number;
                }
            }
        }
        return $minimalNumber;
    }

    private function extractSectionDimension(Par $object, array $dimensions): array {
        $number = $object->getHeadingLevel() - 1;
        $dimensions[$number]++;
        while ($number < self::SECT_NESTED_LEVEL_LIMIT) {
            $number++;
            $dimensions[$number] = 0;
        }
        return $dimensions;
    }

    public static function getMinimalHeadingLevel(): int {
        return self::$minimalHeadingLevel;
    }

    static function getRelationshipById(string $id): string {
        $element = self::$relationshipsXpath->query("//*[@Id='" . $id . "']");
        $target = $element[0]->getAttribute("Target");
        return $target;
    }

    static function getElementStyling(string $constStyleType, string $id): ?string {
        if (self::$stylesXpath) {
            $element = self::$stylesXpath->query("/w:styles/w:style[@w:type='" . $constStyleType . "'][@w:styleId='" . $id . "']")[0];
            $name = self::$stylesXpath->query("w:name", $element)[0];
            return $name->getAttribute("w:val");
        } else {
            return null;
        }
    }

    static function getNumberingTypeById(string $id, string $lvl): ?string {
        if (!self::$numberingXpath) return null; // the numbering styles are missing.
        $element = self::$numberingXpath->query("//*[@w:abstractNumId='" . $id . "']");
        if ($element->count() == 0) return null;
        $level = self::$numberingXpath->query("w:lvl[@w:ilvl='" . $lvl . "']", $element[0]);
        if ($level->count() == 0) return null;
        $type = self::$numberingXpath->query("w:numFmt/@w:val", $level[0]);
        if ($type->count() == 0) return null;
        return $type[0]->nodeValue;
    }

    public function getContent(): array {
        return $this->content;
    }
}
