<?php namespace docx2tei\tei;



use docx2tei\objectModel\DataObject;
use docx2tei\tei\Row as TeiRow;

class Table extends Element {

	public function __construct(DataObject $dataObject) {
		parent::__construct($dataObject);
	}

	public function setContent() {

		// TODO create and append table label and caption

		$tableNode = $this->ownerDocument->createElement('table');
		$this->appendChild($tableNode);

		foreach ($this->getDataObject()->getContent() as $content) {
			$row = new TeiRow($content);
			$tableNode->appendChild($row);
			$row->setContent();
		}
	}
}
