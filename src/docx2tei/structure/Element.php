<?php namespace docx2tei\structure;

use docx2tei\objectModel\body\Par;
use docx2tei\objectModel\DataObject;

abstract class Element extends \DOMElement {
    private $dataObject;

    public function __construct(DataObject $dataObject) {
        $this->dataObject = $dataObject;
        $name = '';
        switch (get_class($dataObject)) {
            case "docx2tei\objectModel\body\Par":
                $types = $dataObject->getType();
                if (in_array(Par::DOCX_PAR_HEADING, $types)) {
                    $name = "title";
                } else {
                    $name = "p";
                }
                break;
            case "docx2tei\objectModel\body\Table":
                $name = 'table-wrap';
                break;
            case "docx2tei\objectModel\body\Row":
                $name = 'row';
                break;
            case "docx2tei\objectModel\body\Cell":
                $name = 'cell';
                break;
            case "docx2tei\objectModel\body\Image":
                $name = 'fig';
        }
        if (!empty($name)) parent::__construct($name);
    }

    protected function getDataObject() {
        return $this->dataObject;
    }
}
