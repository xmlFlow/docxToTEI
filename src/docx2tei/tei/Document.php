<?php namespace docx2tei\tei;



use docx2tei\DOCXArchive;
use docx2tei\tei\Par as TeiPar;
use docx2tei\objectModel\body\Par;
use docx2tei\tei\Table as TeiTable;
use docx2tei\tei\Figure as TeiFigure;
use docx2tei\objectModel\body\Table;
use docx2tei\objectModel\DataObject;
use docx2tei\objectModel\Document as DOCXDocument;

class Document extends \DOMDocument {

	private $docxArchive;


	var $tei;


	var $teiHeader;


	var $text;


	var $back;


	var $sections = array();


	var $lists = array();

	public function __construct(DOCXArchive $docxArchive) {
		parent::__construct('1.0', 'utf-8');
		$this->docxArchive = $docxArchive;
		$this->preserveWhiteSpace = false;
		$this->formatOutput = true;

		// Doctype
		$impl = new \DOMImplementation();
		$this->appendChild($impl->createDocumentType("article", "-//NLM//DTD JATS (Z39.96) Journal Publishing DTD v1.2 20190208//EN", "https://tei.nlm.nih.gov/publishing/1.2/JATS-journalpublishing1.dtd"));

		$this->setBasicStructure();
		$this->extractContent();
		$this->cleanContent();
		$this->extractMetadata();
	}

	public function getTeiFile(string $pathToFile) {
		$this->save($pathToFile);
	}

	private function setBasicStructure() {
		$this->tei = $this->createElement('TEI');
        $this->tei->setAttributeNS(
            "http://www.w3.org/2000/xmlns/",
            "xmlns",
            "http://www.tei-c.org/ns/1.0"
        );
		$this->tei->setAttributeNS(
			"http://www.w3.org/2000/xmlns/",
			"xmlns:xlink",
			"http://www.w3.org/1999/xlink"
		);

		$this->appendChild($this->tei);

		$this->teiHeader = $this->createElement('teiHeader');
		$this->tei->appendChild($this->teiHeader);

		$this->text = $this->createElement('text');
		$this->tei->appendChild($this->text);


	}

	private function extractContent() {
		$document = $this->docxArchive->getDocument();
		if (!empty($document->getContent())) {

			$latestSectionId = array();
			$latestSections = array();

			$subList = array(); // temporary container for sublists
			$listItem = null; // temporary container for previous list item
			$listCounter = -1; // temporary container for current list ID
			foreach ($document->getContent() as $key => $content) {
				$contentId = 'sec-' . implode('_', $content->getDimensionalSectionId());

				// Appending section, must correspond section nested level; TODO optimize with recursion
				if ($content->getDimensionalSectionId() !== $latestSectionId) {
					$sectionNode = $this->createElement("sec");
					$sectionNode->setAttribute('id', $contentId);
					$this->sections[$contentId] = $sectionNode;
					if (count($content->getDimensionalSectionId()) === 1) {
						$this->text->appendChild($sectionNode);
						$latestSections[0] = $sectionNode;
					} elseif (count($content->getDimensionalSectionId()) === 2) {
						$latestSections[0]->appendChild($sectionNode);
						$latestSections[1] = $sectionNode;
					} elseif (count($content->getDimensionalSectionId()) === 3) {
						$latestSections[1]->appendChild($sectionNode);
						$latestSections[2] = $sectionNode;
					} elseif (count($content->getDimensionalSectionId()) === 4) {
						$latestSections[2]->appendChild($sectionNode);
						$latestSections[3] = $sectionNode;
					} elseif (count($content->getDimensionalSectionId()) === 5) {
						$latestSections[3]->appendChild($sectionNode);
					}

					$latestSectionId = $content->getDimensionalSectionId();
				}

				// If there aren't any sections, append content to the body
				if (empty($this->sections)) {
					$sectionsOrBody = array($this->text);
				} else {
					$sectionsOrBody = $this->sections;
				}

				switch (get_class($content)) {

					case "docx2tei\objectModel\body\Par":

						$teiPar = new TeiPar($content);

						foreach ($sectionsOrBody as $section) {
							if ($contentId === $section->getAttribute('id') || $section->nodeName === "body") {
								if (!in_array(Par::DOCX_PAR_LIST, $content->getType())) {
									$section->appendChild($teiPar);
									$teiPar->setContent();
								} elseif (!in_array(Par::DOCX_PAR_HEADING, $content->getType())) {
									$itemId = $content->getNumberingItemProp()[Par::DOCX_LIST_ITEM_ID];
									$hasSublist = $content->getNumberingItemProp()[Par::DOCX_LIST_HAS_SUBLIST];

									// Creating and appending new list
									// !array_key_exists... is necessary as there can be several lists with the same id, usually it's malformed doc
									// TODO find a way to properly deal with numberings with the same id interrupted by simple regular paragraphs
									if ($listCounter !== $content->getNumberingId()) {
										$newList = $this->createElement('list');
										if ($content->getNumberingType() == Par::DOCX_LIST_TYPE_ORDERED) {
											$newList->setAttribute("list-type", "order");
										} else {
											$newList->setAttribute("list-type", "bullet");
										}
										$this->lists[$content->getNumberingId()] = $newList;
									}

									$section->appendChild($this->lists[$content->getNumberingId()]);

									// appends nested lists and list items based on their level
									if (count($itemId) === $content->getNumberingLevel()+1) {
										$listItem = $this->createElement('list-item');
										$listItem->appendChild($teiPar);
										$teiPar->setContent();

										if ($content->getNumberingLevel() === 0) {
											$this->lists[$content->getNumberingId()]->appendChild($listItem);
										} elseif (array_key_exists($content->getNumberingLevel()-1, $subList)) {
											$subList[$content->getNumberingLevel()-1]->appendChild($listItem);
											// Append to first list level if user has set unrealistic level for nested items
										} else {
											$this->lists[$content->getNumberingId()]->appendChild($listItem);
										}

										if ($hasSublist) {
											$subList[$content->getNumberingLevel()] = $this->createElement('list');
											if ($content->getNumberingType() == Par::DOCX_LIST_TYPE_ORDERED) {
												$subList[$content->getNumberingLevel()]->setAttribute("list-type", "order");
											} else {
												$subList[$content->getNumberingLevel()]->setAttribute("list-type", "bullet");
											}
											$listItem->appendChild($subList[$content->getNumberingLevel()]);
										}
									}

									// Refreshing list-item ID number
									$listCounter = $content->getNumberingId();
								}
							}
						}
						break;
					case "docx2tei\objectModel\body\Table":
						foreach ($sectionsOrBody as $section) {
							if ($contentId === $section->getAttribute('id') || $section->nodeName === "body") {
								$table = new TeiTable($content);
								$section->appendChild($table);
								$table->setContent();

							}
						}
						break;
					case "docx2tei\objectModel\body\Image":
						foreach ($sectionsOrBody as $section) {
							if ($contentId === $section->getAttribute('id') || $section->nodeName === "body") {
								$figure = new TeiFigure($content);
								$section->appendChild($figure);
								$figure->setContent();
							}
						}
				}
			}
		}
	}



	private function cleanContent(): void {
		$xpath = new \DOMXPath($this);
		$nodesToRemove = $xpath->query("//body//*[not(normalize-space()) and not(.//@*) and not(self::td)]");
		foreach ($nodesToRemove as $nodeToRemove) {
			$nodeToRemove->parentNode->removeChild($nodeToRemove);
		}
	}

	private function extractMetadata() {
		//TODO find and extract OOXML metadata

		// Needed to make JATS XML document valid
		$journalMetaNode = $this->createElement("journal-meta");
		$this->teiHeader->appendChild($journalMetaNode);
		$journalIdNode = $this->createElement("journal-id");
		$journalMetaNode->appendChild($journalIdNode);
		$issnNode = $this->createElement("issn");
		$journalMetaNode->appendChild($issnNode);

		$articleMetaNode = $this->createElement("article-meta");
		$this->teiHeader->appendChild($articleMetaNode);
		$titleGroupNode = $this->createElement("title-group");
		$articleMetaNode->appendChild($titleGroupNode);
		$articleTitleNode = $this->createElement("article-title");
		$titleGroupNode->appendChild($articleTitleNode);
	}
}
