<?php namespace docx2jats\jats;



use docx2jats\objectModel\DataObject;
use docx2jats\objectModel\body\Par;

abstract class Element extends \DOMElement {

	private $dataObject;

	public function __construct(DataObject $dataObject) {

		$this->dataObject = $dataObject;

		// Determing element name
		$name = '';
		switch (get_class($dataObject)) {
			case "docx2jats\objectModel\body\Par":


				$types = $dataObject->getType();
				if (in_array(Par::DOCX_PAR_HEADING, $types)) {
					$name = "title";
				} else {
					$name = "p";
				}
				break;
			case "docx2jats\objectModel\body\Table":
				$name = 'table-wrap';
				break;
			case "docx2jats\objectModel\body\Row":
				$name = 'tr';
				break;
			case "docx2jats\objectModel\body\Cell":
				$name = 'td';
				break;
			case "docx2jats\objectModel\body\Image":
				$name = 'fig';
		}



		if (!empty($name)) parent::__construct($name);
	}

	protected function getDataObject() {
		return $this->dataObject;
	}
}
