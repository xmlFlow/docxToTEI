<?php namespace docx2tei\structure;



use docx2tei\objectModel\DataObject;
use docx2tei\structure\Cell as TeiCell;

class Row extends Element {
	public function __construct(DataObject $dataObject) {
		parent::__construct($dataObject);
	}

	public function setContent() {
		foreach ($this->getDataObject()->getContent() as $content) {
			$cell = new TeiCell($content);
			$this->appendChild($cell);
			$cell->setContent();
		}
	}
}
