<?php namespace docx2tei\tei;



use docx2tei\objectModel\DataObject;
use docx2tei\tei\Cell as JatsCell;

class Row extends Element {
	public function __construct(DataObject $dataObject) {
		parent::__construct($dataObject);
	}

	public function setContent() {
		foreach ($this->getDataObject()->getContent() as $content) {
			$cell = new JatsCell($content);
			$this->appendChild($cell);
			$cell->setContent();
		}
	}
}
