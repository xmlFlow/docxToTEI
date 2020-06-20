<?php namespace docx2jats\objectModel\body;



use docx2jats\objectModel\DataObject;
use docx2jats\objectModel\Document;

class Text extends DataObject {
	const DOCX_TEXT_BOLD = 1;
	const DOCX_TEXT_ITALIC = 2;
	const DOCX_TEXT_SUPERSCRIPT = 3;
	const DOCX_TEXT_SUBSCRIPT = 4;
	const DOCX_TEXT_STRIKETHROUGH = 5;
	const DOCX_TEXT_EXTLINK = 6;

	private $properties;
	private $text;
	private $type = array();
	private $link;

	public function __construct(\DOMElement $domElement) {
		parent::__construct($domElement);
		$this->properties = $this->setProperties('w:rPr/child::node()');
		$this->text = $this->setText('w:t');
		$this->type = $this->setType();
	}



	private function setText(string $xpathExpression) {
		$stringText = '';
		$contentNodes = $this->getXpath()->evaluate($xpathExpression, $this->getDomElement());

		foreach ($contentNodes as $contentNode) {
			$stringText = $stringText . $contentNode->nodeValue;
		}

		return $stringText;
	}


	public function getContent(): string {
		return $this->text;
	}


	public function getProperties(): array {
		return $this->properties;
	}


	private function togglePropertyEnabled(\DOMElement $property): bool {
		if ($property->hasAttribute('w:val')) {
			$attrValue = $property->getAttribute('w:val');
			return ($attrValue == '1' || $attrValue == 'true');
		} else {
			return true; // No value means it's enabled
		}
	}


	private function setType() {
		$type = array();

		$properties = $this->getXpath()->query('w:rPr/child::node()', $this->getDomElement());
		foreach ($properties as $property) {
			switch($property->nodeName) {
				case "w:b":
					if ($this->togglePropertyEnabled($property)) {
						$type[] = $this::DOCX_TEXT_BOLD;
					}
					break;
				case "w:i":
					if ($this->togglePropertyEnabled($property)) {
						$type[] = $this::DOCX_TEXT_ITALIC;
					}
					break;
				case "w:vertAlign":
					if ($property->hasAttribute('w:val')) {
						$attrValue = $property->getAttribute('w:val');
						if ($attrValue === "superscript") {
							$type[] = $this::DOCX_TEXT_SUPERSCRIPT;
						} elseif ($attrValue === "subscript") {
							$type[] = $this::DOCX_TEXT_SUBSCRIPT;
						}
					}
					break;
				case "w:strike":
					if ($this->togglePropertyEnabled($property)) {
						$type[] = $this::DOCX_TEXT_STRIKETHROUGH;
					}
					break;
			}
		}

		return $type;
	}

	public function addType(string $type): void {
		$this->type[] = $type;
	}


	public function getType(): array {
		return $this->type;
	}

	function setLink(): void {
		$parent = $this->getDomElement()->parentNode;
		if ($parent->tagName == "w:hyperlink") {
			$ref = $parent->getAttribute("r:id");
			// TODO link by other attributes for identification, e.g. w:anchor
			if ($ref) {
				$this->link = Document::getRelationshipById($ref);
			}
		}
	}

	public function getLink(): ?string {
		return $this->link;
	}
}
