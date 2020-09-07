<?php namespace docx2tei\tei;



use docx2tei\objectModel\DataObject;
use docx2tei\tei\Text as TeiText;

class Par extends Element {

	public function __construct(DataObject $dataObject)
	{
		parent::__construct($dataObject);

	}

	public function setContent() {

		foreach ($this->getDataObject()->getContent() as $content) {
			TeiText::extractText($content, $this);
		}
	}
}
