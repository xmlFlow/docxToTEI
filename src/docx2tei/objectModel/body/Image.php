<?php namespace docx2tei\objectModel\body;



use docx2tei\objectModel\DataObject;
use docx2tei\objectModel\Document;

class Image extends DataObject {


	private $link;

	public function __construct(\DOMElement $domElement) {
		parent::__construct($domElement);

		$this->link = $this->extractLink();

	}

	private function extractLink(): ?string {
		$link = null;
		$relationshipId = null;

		$this->getXpath()->registerNamespace("a", "http://schemas.openxmlformats.org/drawingml/2006/main");
		$linkElement = $this->getFirstElementByXpath(".//a:blip", $this->getDomElement());
		if ($linkElement && $linkElement->hasAttribute("r:embed")) {
			$relationshipId = $linkElement->getAttribute("r:embed");
		}

		if ($relationshipId) {
			$link = Document::getRelationshipById($relationshipId);
		}

		return $link;
	}

	public function getLink(): ?string {
		return $this->link;
	}

	public function getFileName(): ?string {
		$name = basename($this->link);
		return $name;
	}

}
