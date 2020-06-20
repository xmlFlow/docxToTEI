<?php namespace docx2jats\jats;



use docx2jats\objectModel\DataObject;
use docx2jats\objectModel\body\Image as FigureObject;

class Figure extends Element {

	var $figureObject;

	public function __construct(DataObject $dataObject) {
		parent::__construct($dataObject);

		$this->figureObject = $dataObject;
	}

	function setContent() {

		$figureNode = $this->ownerDocument->createElement('graphic');
		$this->appendChild($figureNode);

		$pathInfo = pathinfo($this->figureObject->getLink());

		$figureNode->setAttribute("mimetype", "image");

		switch ($pathInfo['extension']) {
			case "jpg":
			case "jpeg":
				$figureNode->setAttribute("mime-subtype", "jpeg");
				break;
			case "png":
				$figureNode->setAttribute("mime-subtype", "png");
				break;
		}

		$figureNode->setAttribute("xlink:href", $pathInfo['basename']);
	}
}
