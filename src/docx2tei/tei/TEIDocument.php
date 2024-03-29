<?php namespace docx2tei\tei;

use docx2tei\structure\Document;
use docx2tei\XMLUtils;
use DOMDocument;
use DOMXPath;

class TEIDocument extends DOMDocument {
    var $cfg;
    var $root;
    var $teiHeader;
    var $facsimile;
    var $text;
    var $body;
    var $structuredDocument;
    var $xpath;
    var $headers = array();
    var $newDom;

    public function __construct(Document $structuredDoc, $config) {
        $this->structuredDocument = $structuredDoc;
        $this->xpath = new DOMXPath($structuredDoc);
        $this->cfg = $config;
        parent::__construct('1.0', 'utf-8');
        $this->preserveWhiteSpace = false;
        $this->formatOutput = true;
        $this->getHeaders();
        $this->setStructure();
        $this->isCorrectStructure();
        # Section processing
        $this->newDom = new Headers($this, $this->headers);
        $this->newDom = new Facsimiles($this);
        $this->newDom = new Abstracts($this);
        $this->newDom = new Edition($this);
        $this->newDom = new EnglishTranslation($this);
        $this->newDom = new Synopsis($this);
        $this->newDom = new Commentary($this);
# Final  processing
        $finalDom = new FinalDocument($this);
        $this->newDom = $finalDom->getDocumentElement();

        $x = 1;
    }

    function getHeaders(): void {
        $metadataFields = $this->xpath->query('//root/text/sec/title[text()="' . $this->cfg->sections->metadata . '"]/parent::sec/table-wrap/table/row');
        $definedHeaders = [];
        foreach ($metadataFields as $metadata) {
            $cells = $metadata->getElementsByTagName("cell");
            if (count($cells) == 2) {
                $headerName = trim($cells->item(0)->textContent);
                $value = trim($cells->item(1)->textContent);
                $config_headers = get_object_vars($this->cfg->headers);
                $key = array_search($headerName, array_values($config_headers));
                if ($key >= 0) {
                    $this->headers[array_keys($config_headers)[$key]] = trim($value);
                    $definedHeaders [] = $headerName;
                } else {
                    XMLUtils::print_error("[Error] Not allowed header in the metadata: " . $headerName);
                }

            } else {
                XMLUtils::print_error("Metadata table should be 2 columns wide");
            }
        }
        $unfefined = array_diff(array_values($config_headers) , $definedHeaders);
        $x=1;

    }

    function setStructure() {
        $this->root = $this->createElement('TEI');
        $this->root->setAttributeNS(
            "http://www.w3.org/2000/xmlns/",
            "xmlns",
            "http://www.tei-c.org/ns/1.0"
        );
        $this->root->setAttribute('xml:id', $this->headers["h4"] ?? "");
        $this->appendChild($this->root);
        $this->teiHeader = $this->createElement('teiHeader');
        $this->root->appendChild($this->teiHeader);
        $this->facsimile = $this->createElement('facsimile');
        $this->root->appendChild($this->facsimile);
        $this->text = $this->createElement('text');
        $this->body = $this->createElement('body');
        $this->text->appendChild($this->body);
        $this->root->appendChild($this->text);
    }

    function isCorrectStructure(): bool {
        $correct = true;
        $correct = $this->isCorrectSections();
        $correct = $this->isCorrectHeaders();
        return $correct;
    }

    /**
     * @return bool
     */
    function isCorrectSections(): bool {
        $sectionNodes = $this->xpath->query("//root/text/sec/title");
        foreach ($sectionNodes as $section) {
            if (!in_array($section->nodeValue, (array)$this->cfg->sections)) {
                // specially handle Editions
                if (!preg_match("/Edition(\s)*\((.)*\)/i", $section->nodeValue)) {
                    XMLUtils::print_error("[Error] Section missing or wrong : " . $section->nodeValue);
                    return false;
                }
            }
        }
        return true;
    }

    function isCorrectHeaders(): bool {
        return true;
    }

    function renameElement($element, $newName) {
        $newElement = $element->ownerDocument->createElement($newName);
        $parentElement = $element->parentNode;
        $parentElement->insertBefore($newElement, $element);
        $childNodes = $element->childNodes;
        while ($childNodes->length > 0) {
            $newElement->appendChild($childNodes->item(0));
        }
        $attributes = $element->attributes;
        while ($attributes->length > 0) {
            $attribute = $attributes->item(0);
            if (!is_null($attribute->namespaceURI)) {
                $newElement->setAttributeNS('http://www.w3.org/2000/xmlns/',
                    'xmlns:' . $attribute->prefix,
                    $attribute->namespaceURI);
            }
            $newElement->setAttributeNode($attribute);
        }
        $parentElement->removeChild($element);
        return $newElement;
    }
    function domNodesToArray(array $tags, \DOMXPath $xpath)
    {
        $tagNameToArr = [];
        foreach ($tags as $tag) {
            $tagData = [];
            $attrs = $tag->attributes ? iterator_to_array($tag->attributes) : [];
            $subTags = $tag->childNodes ? iterator_to_array($tag->childNodes) : [];
            foreach ($xpath->query('namespace::*', $tag) as $nsNode) {
                if ($tag->hasAttribute($nsNode->nodeName)) {
                    $attrs[] = $nsNode;
                }
            }

            foreach ($attrs as $attr) {
                $tagData[$attr->nodeName] = $attr->nodeValue;
            }
            if (count($subTags) === 1 && $subTags[0] instanceof \DOMText) {
                $text = $subTags[0]->nodeValue;
            } elseif (count($subTags) === 0) {
                $text = '';
            } else {
                // ignore whitespace (and any other text if any) between nodes
                $isNotDomText = function($node){return !($node instanceof \DOMText);};
                $realNodes = array_filter($subTags, $isNotDomText);
                $subTagNameToArr = $this->domNodesToArray($realNodes, $xpath);
                $tagData = array_merge($tagData, $subTagNameToArr);
                $text = null;
            }
            if (!is_null($text)) {
                if ($attrs) {
                    if ($text) {
                        $tagData['_'] = $text;
                    }
                } else {
                    $tagData = $text;
                }
            }
            $keyName = $tag->nodeName;
            $tagNameToArr[$keyName][] = $tagData;
        }
        return $tagNameToArr;
    }
    function xmlToArr(string $xml)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $xpath = new \DOMXPath($doc);
        $tags = $doc->childNodes ? iterator_to_array($doc->childNodes) : [];
        return $this->domNodesToArray($tags, $xpath);
    }

    public function saveToFile(string $pathToFile) {
        $s = $this->newDom->saveXML();

        $doc = new \DOMDocument("1.0");
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->loadXML($s);
        $s = $doc->saveXML();
        $s = str_replace('&amp;#x200c;','&#x200c;',$s);
        $s = str_replace('&amp;#8205;','&#8205;',$s);
        file_put_contents($pathToFile, $s);
    }


}
