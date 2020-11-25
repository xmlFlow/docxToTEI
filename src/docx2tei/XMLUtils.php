<?php

namespace docx2tei;

use DOMDocument;

class XMLUtils {
    protected static $bnd = '#';

    public function __construct() {
    }

    /**
     * @param $s
     * @return string|string[]|null
     */
    public static function cleanMultipleSpaces(string $s) {
        return preg_replace('/\s+/i', ' ', $s);
    }

    /**
     * @param string $content
     * @return string|string[]|null
     */
    public static function tagReplace(string $content, string $tag, string $replace) {
        return preg_replace('/<' . $tag . '>(.*)<\/' . $tag . '>/i', '<' . $replace . '>$1</' . $replace . '>', $content);
    }

    /**
     * @param $dom
     * @param $elementName
     * @return mixed
     */
    public static function  removeTagByNameLeaveChildren($dom, $elementName){
        $xpath = new \DOMXPath($dom);

        foreach ($xpath->query('//'.$elementName) as $node) {
            $parent = $node->parentNode;
            while ($node->hasChildNodes()) {
                $parent->insertBefore($node->lastChild, $node->nextSibling);
            }
            $parent->removeChild($node);
        }
        return $dom;
    }

    /**
     * @param $s
     * @return string|string[]|null
     */
    public static function createFootnoteTags(string $s) {
        preg_replace('/&lt;note place="end"&gt;/i', '<note place="end">', $s);
        preg_replace('/&lt;\/note&gt;/i', '</note>', $s);
        preg_replace('/&lt;foreign&gt;/i', '<foreign>', $s);
        preg_replace('/&lt;\/foreign&gt;/i', '</foreign>', $s);

        return $s;
    }


    /**
     * @param $s
     * @return string|string[]|null
     */
    public static function createComplexSentence(string $s) {
        preg_match_all('/#SB(.|\n)*?#SE/i', $s, $matches);
        $match = $matches[0];
        if (!is_null($match) && count($match) != 0) {
            $s = preg_replace('/#SB/i', '<s>', $s);
            $s = preg_replace('/#SE/i', '</s>', $s);
        }

        return $s;
    }

    /**
     * @param $dom
     * @param $tag
     */
    public static  function removeTagByName($dom, $tag): void {
        $titles = $dom->getElementsByTagName($tag);
        while ($titles->length > 0) {
            $node = $titles->item(0);
            $node->parentNode->removeChild($node);
        }

    }

    /**
     * @param $s
     * @return string|string[]|null
     */
    public static function createLineBegin(string $s) {
        $s = preg_replace('/<p>(\s)*<\/p>/i', '<lb/>', $s);
        return $s;
    }

    /**
     * @param string $s
     * @return string|string[]|null
     */
    public static function createLineBeginNoBreak(string $s) {
        $s = preg_replace('/<p>(\s)*[-]+(\s)*<\/p>/i', '<lb break="no"/>', $s);
        return $s;
    }

    public static function createDot(string $s) {
        $s = preg_replace('/\./i', '<orig>.</orig>', $s);
        return $s;
    }


    public static function createWords(string $s) {
        if (preg_match("/[\p{Devanagari}]+/u", $s,$matches)) {
            $s = preg_replace('/[\p{Devanagari}]+/u', '<w>'.$matches[0].'</w>', $s);
        }
        return $s;
    }

    /**
     * @param string $s
     * @return string|string[]|null
     */
    public static function joinLines(string $s) {
        $s = preg_replace('/\r|\n/', '', $s);
        return $s;
    }

    public static function createControlledVocabs(string $s) {
        $tags = self::getControlledVocabList();
        preg_match_all('/' . XMLUtils::$bnd . '[\w|?|&amp;]+(@(.)*)*(\{(.)*\})+' . XMLUtils::$bnd . '/iUu', $s, $matches);
        $match = $matches[0];
        if (!is_null($match) && count($match) != 0) {
            $str = str_replace(XMLUtils::$bnd, '', $match[0]);
            $parts = explode("{", $str);
            $suffix1 = str_replace('}', '', $parts[1]);
            $prefix = explode('@', $parts[0]);
            $tagName = $prefix[0];
            $tagName = self::removeUnnecessaryChars($tagName);
            $elem = new DOMDocument();
            foreach ($tags as $tag) {
                if ($tag["original"] == $tagName) {
                    $tagName = str_replace($tag ["original"], $tag["replace"], $tagName);
                    $tagElem = $elem->createElement($tagName);
                    $attr = $elem->createAttribute("corresp");
                    $attr->value = $suffix1;
                    $tagElem->appendChild($attr);
                }
            }
            $s = str_replace($matches[0], $tagElem->ownerDocument->saveXML($tagElem), $s);
        }
        return $s;
    }

    public static function getControlledVocabList(): array {
        $tags = array();
        return $tags;
    }

    /**
     * @param string $tagName
     * @return string|string[]
     */
    public static function removeUnnecessaryChars(string $tagName) {
        $tagName = str_replace('=', '', $tagName);
        $tagName = str_replace('-', '', $tagName);
        return $tagName;
    }

    /**
     * @param string $s
     * @return string
     */
    public static function createStructuredContent(string $s) {
        $tags = self::getTagsList();
        preg_match_all('/' . XMLUtils::$bnd . '[\w|?|&amp;]+(@(.)*)*(\{(.)*\})+' . XMLUtils::$bnd . '/iUu', $s, $matches);
        $match = $matches[0];
        if (!is_null($match) && count($match) != 0) {
            $str = str_replace(XMLUtils::$bnd, '', $match[0]);
            $parts = explode("{", $str);
            $suffix1 = str_replace('}', '', $parts[1]);
            if (count($parts) == 3) {
                $suffix2 = str_replace('}', '', $parts[2]);
            }
            $prefix = explode('@', $parts[0]);
            $tagName = $prefix[0];
            $tagName = self::removeUnnecessaryChars($tagName);
            $elem = new DOMDocument();
            foreach ($tags as $tag) {
                if ($tag["original"] == $tagName) {
                    $tagName = str_replace($tag ["original"], $tag["replace"], $tagName);
                    $tagElem = $elem->createElement($tagName);
                    // remove tag from array
                    array_shift($prefix);
                    for ($i = 0; $i < count($tag["attributes"]); $i++) {
                        if ($i < count($tag["attributes"])) {
                            $attr = $elem->createAttribute($tag["attributes"][$i]['tag']);
                            $val = $tag["attributes"][$i]['default'];
                            if ((count($prefix) > $i) && (strlen($prefix[$i]) > 0)) {
                                $val = $prefix[$i];
                            }
                            $attr->value = $val;
                            $tagElem->appendChild($attr);
                        }
                    }
                    for ($i = count($tag["attributes"]); $i < count($prefix); $i++) {
                        $extraAttrs = explode("=", $prefix[$i]);
                        if (count($extraAttrs) == 2) {
                            $attr = $elem->createAttribute($extraAttrs[0]);
                            $attr->value = $extraAttrs[1];
                            $tagElem->appendChild($attr);
                        } else {
                            self::print_error('Attribute with no = sign ' . $prefix[$i]);
                        }
                    }
                    if (array_key_exists("innerTags", $tag) && count($tag["innerTags"]) == 2) {
                        $suffix1Elem = $elem->createElement($tag["innerTags"][0], $suffix1);
                        $tagElem->appendChild($suffix1Elem);
                        if (isset($suffix2)) {
                            $suffix2Elem = $elem->createElement($tag["innerTags"][1], $suffix2);
                            $tagElem->appendChild($suffix2Elem);
                        }
                    } else {
                        $tagElem->nodeValue = $suffix1;
                    }
                    $s = str_replace($matches[0], $tagElem->ownerDocument->saveXML($tagElem), $s);
                }
            }
        }
        return $s;
    }

    /**
     * @return array[]
     */
    private static function getTagsList(): array {
        $tags = [
            array(
                "original" => "&amp;",
                "replace" => "add",
                "attributes" => array(
                    array("tag" => "place", "default" => "above the line"),
                    array("tag" => "hand", "default" => "first")
                )
            ),
            array(
                "original" => "?",
                "replace" => "unclear",
                "attributes" => array(
                    array("tag" => "cert", "default" => "high"),
                )
            ),
            array(
                "original" => "orig",
                "replace" => "orig",
                "attributes" => array()
            ),
            array(
                "original" => "sur",
                "replace" => "surplus",
                "attributes" => array(
                    array("tag" => "reason", "default" => "repeated"),
                )
            ),
            array(
                "original" => "sup",
                "replace" => "supplied",
                "attributes" => array(
                    array("tag" => "reason", "default" => "lost"),
                )
            ),
            array(
                "original" => "del",
                "replace" => "del",
                "attributes" => array(
                    array("tag" => "rend", "default" => "overstrike"),
                )
            ),
            array(
                "original" => "sb",
                "replace" => "sb",
                "attributes" => array()
            ),
            array(
                "original" => "cor",
                "replace" => "choice",
                "innerTags" => array('sic', 'corr'),
                "attributes" => array()
            ),
            array(
                "original" => "reg",
                "replace" => "choice",
                "innerTags" => array('orig', 'corr'),
                "attributes" => array()
            ),
            array(
                "original" => "pen",
                "replace" => "persName",
                "attributes" => array()
            ),
            array(
                "original" => "pln",
                "replace" => "placeName",
                "attributes" => array()
            ),
            array(
                "original" => "gen",
                "replace" => "geogName",
                "attributes" => array()
            )
        ];
        return $tags;
    }

    /**
     * @param $value
     */
    public static function print_error($message): void {
        echo("" . $message . "\n");
        //exit();
        //error_log($message."\n");
    }

    /**
     * @param string $s
     * @return string|string[]
     */
    public static function createSpaces(string $s) {
        preg_match_all('/' . XMLUtils::$bnd . '(\.)+([\@][((\w|=)>\s)]*)*' . XMLUtils::$bnd . '/i', $s, $matches);
        $match = $matches[0];
        if (!is_null($match) && count($match) != 0) {
            $elem = new DOMDocument();
            $gap = $elem->createElement("space");
            $gapsLength = strlen($match[0]);
            $qn = $elem->createAttribute('quantity');
            $qn->value = $gapsLength;
            $gap->appendChild($qn);
            $unit = $elem->createAttribute('unit');
            $unit->value = 'chars';
            $gap->appendChild($unit);
            $s = str_replace($matches[0], $gap->ownerDocument->saveXML($gap), $s);
        }
        return $s;
    }

    /**
     * @param string $s
     * @param string $reason
     * @param string $replace
     * @return string|string[]
     */
    public static function createGap(string $s, string $reason, string $replace) {
        preg_match_all('/' . XMLUtils::$bnd . '(' . $replace . ')+([\@][((\w|=)>\s)]*)*' . XMLUtils::$bnd . '/i', $s, $matches);
        $gap = $matches[0];
        if (!is_null($gap) && count($gap) != 0) {
            $str = str_replace(XMLUtils::$bnd, '', $gap[0]);
            $elem = new DOMDocument();
            $gap = $elem->createElement("gap");
            $r = $elem->createAttribute('reason');
            $r->value = $reason;
            $gap->appendChild($r);
            $parts = explode("@", $str);
            if (!is_null($parts)) {
                $gapsLength = strlen(array_shift($parts));
                $ex = $elem->createAttribute('extent');
                $type = ($gapsLength == 1) ? 'character' : 'characters';
                $extent = array_shift($parts);
                if (strlen($extent) > 0) {
                    $type = $extent;
                }
                $extentVAl = $gapsLength . ' ' . $type;
                $ex->value = $extentVAl;
                $gap->appendChild($ex);
            }
            if (count($parts) > 0) {
                $agent = array_shift($parts);
                if (!is_null($agent)) {
                    $ag = $elem->createAttribute('agent');
                    $ag->value = $agent;
                    $gap->appendChild($ag);
                }
            }
            if (count($parts) > 0) {
                for ($i = 0; $i < count($parts); $i++) {
                    $extras = explode('=', $parts[$i]);
                    if (count($extras) == 2) {
                        $attr = $elem->createAttribute($extras[0]);
                        $attr->value = $extras[1];
                        $gap->appendChild($attr);
                    } else {
                        self::print_error($parts[$i] . " does not contain a = sign");
                    }
                }
            }
            $s = str_replace($matches[0], $gap->ownerDocument->saveXML($gap), $s);
        }
        return $s;
    }
}
