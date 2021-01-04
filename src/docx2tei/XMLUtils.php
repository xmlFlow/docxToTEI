<?php

namespace docx2tei;

use DOMDocument;
use DOMXPath;
use Exception;

class XMLUtils {
    static $bnd = '#';

    public function __construct() {
    }

    /**
     * @param $s
     * @return string|string[]|null
     */
    public static function getMarkups(string $s) {
        $s = self::removeZWNJ($s);

        # create gaps of illegible and lost characters
        $s = XMLUtils::createGap('gap', 'reason', 'extent', 'agent', $s, 'lost', '\/');
        $s = XMLUtils::createGap('gap', 'reason', 'extent', 'agent', $s, 'illegible', '\+');
        # create spaces
        $s = XMLUtils::createGap('space', 'unit', 'quantity', '', $s, 'chars', '\.');
        # 2 times
        $s = XMLUtils::createAddElement($s);
        $s = XMLUtils::createAddElement($s);
        $s = XMLUtils::createStructuredContent($s);
        $s = XMLUtils::createDot($s);

        #TODO

        return $s;
    }

    /**
     * @param string $s
     * @return string|string[]|null
     */
    private static function removeZWNJ(string $s) {
        return preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $s);
    }

    /**
     * @param string $tagName
     * @param string $attrOne
     * @param string $attrTwo
     * @param string $attrThree
     * @param string $s
     * @param string $attrOneDefault
     * @param string $countCharType
     * @return string|string[]
     */
    public static function createGap(string $tagName, string $attrOne, string $attrTwo, string $attrThree, string $s, string $attrOneDefault, string $countCharType) {
        preg_match_all('/' . XMLUtils::$bnd . '(' . $countCharType . ')+([\@][((\w|=)>\s)]*)*' . XMLUtils::$bnd . '/i', $s, $matches);
        $match = $matches[0];
        foreach ($match as $m) {

            $str = str_replace(XMLUtils::$bnd, '', $m);
            $elem = new DOMDocument();
            $gap = $elem->createElement($tagName);

            $parts = explode("@", $str);

            $ex = $elem->createAttribute($attrTwo);
            $gapCharacters = array_shift($parts);
            $gapsLength = substr_count($gapCharacters, str_replace('\\', '', $countCharType));
            $characterType = array_shift($parts);
            if ($tagName == "gap") {
                if (is_null($characterType) | strlen($characterType) == 0) {
                    $chars = ($gapsLength == 1) ? 'character' : 'characters';
                    $gapsLength = $gapsLength . ' ' . $chars;

                } else {

                    $gapsLength = $gapsLength . ' ' . $characterType;
                }
            }
            $ex->value = $gapsLength;
            $gap->appendChild($ex);
            if (count($parts) > 0 && strlen($attrThree) > 0) {
                $agent = array_shift($parts);
                if (!is_null($agent)) {
                    $ag = $elem->createAttribute($attrThree);
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

            $r = $elem->createAttribute($attrOne);
            // for space unit
            if ($tagName == "space" && strlen($characterType) > 0) {
                $r->value = $characterType;
            } else {
                $r->value = $attrOneDefault;
            }
            $gap->appendChild($r);


            $count = 1;
            $s = str_replace($m, $gap->ownerDocument->saveXML($gap), $s, $count);
        }
        return $s;
    }

    /**
     * @param string $s
     * @return string
     */
    public static function createAddElement(string $s) {

        $s = preg_replace_callback(
            '/#\&amp;([@\w]{0,}){(.*)}#(\p{Devanagari}*)/U',
            function ($matches) {
                $parts = explode('@', $matches[1]);
                $place = (count($parts) > 1 && strlen($parts[1]) > 0) ? $parts[1] : "above_the_line";
                $hand = (count($parts) > 2 && strlen($parts[2]) > 0) ? $parts[2] : "first";
                return '<w><add place="' . $place . '"  hand="' . $hand . '">' . str_replace("\n", "", $matches[2]) . '</add>' . $matches[3] . '</w>';
            },
            $s
        );
        return $s;

    }

    /**
     * @param string $s
     * @return string
     */
    public static function createStructuredContent(string $s) {
        $tags = self::getTagsList();
        $s = preg_replace('/\s+/i', ' ', $s);
        $pattern = '/' . XMLUtils::$bnd . '[\w|?|]+(@(\w)*)*(\{(.)*\})+' . XMLUtils::$bnd . '/U';

        # Ungready is very important
        preg_match_all($pattern, $s, $matches);
        $match = $matches[0];
        if (!is_null($match) && count($match) != 0) {
            foreach ($match as $m) {
                $hash_count = substr_count($m, XMLUtils::$bnd);
                if ($hash_count % 2 == 0) {
                    $content = self::createStructuredContent(trim($m, XMLUtils::$bnd));
                } else if ($hash_count == 3) {
                    $content = self::createStructuredContent(self::str_replace_n(XMLUtils::$bnd, "", $m, 1));
                }
                $parts = explode("{", $content);
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
                                self::print_error('[Error] Attribute with no = sign ' . $prefix[$i]);
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
                        $s = str_replace($m, $tagElem->ownerDocument->saveXML($tagElem), $s);
                    }
                }
            }
        }
        return $s;
    }

    public static function createDot(string $s) {
        $s = preg_replace('/\•/U', '<orig>•</orig>', $s);
        return $s;
    }

    /**
     * @param $value
     */
    public static function print_error($message, $exit = false): void {
        echo("" . $message . "\n");
        if ($exit) {
            exit("[Error] Please correct your Microsoft Word file  and upload again");
        }
    }

    /**
     * @return array[]
     */
    private static function getTagsList(): array {
        $tags = [
            /*array(
                "original" => "&amp;",
                "replace" => "add",
                "attributes" => array(
                    array("tag" => "place", "default" => "above_the_line"),
                    array("tag" => "hand", "default" => "first")
                )
            ),*/
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
                "attributes" => array()
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
                    array("tag" => "rend", "default" => "crossed_out"),
                )
            ),
            array(
                "original" => "ref",
                "replace" => "ref",
                "attributes" => array(
                    array("tag" => "target"),
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
                "innerTags" => array('orig', 'reg'),
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
     * String replace nth occurrence
     *
     * @param type $search Search string
     * @param type $replace Replace string
     * @param type $subject Source string
     * @param type $occurrence Nth occurrence
     * @return type        Replaced string
     */
    public static function str_replace_n($search, $replace, $subject, $occurrence) {
        $search = preg_quote($search);
        return preg_replace("/^((?:(?:.*?$search){" . --$occurrence . "}.*?))$search/", "$1$replace", $subject);
    }

    public static function removeLastElementOfParent($dom, string $tag) {
        $xpath = new DOMXPath($dom);
        $lastLbs = $xpath->query('//ab/' . $tag . '[last()]');
        foreach ($lastLbs as $lb) {
            $parent = $lb->parentNode;
            $parent->removeChild($lb);
        }
        return $dom;

    }
    public static function removeUnnecessaryChars(string $tag) {
        $tag = str_replace('=', '', $tag);
        $tag = str_replace('-', '', $tag);
        return $tag;
    }

    public static function createLBBreakForMinus(string $s) {
        $s = preg_replace_callback_array(
            [
                '/-\s*(<\/p>|#SE)/U' => function ($match) {
                return '<lb break="no"/>' . $match[1];
            },
                '/<p>(.*)(?!(-|<lb break=\"no\"\/>))<\/p>/U' => function ($match) {
                    if (substr_count($match[0], '<lb break="no"/>') > 0) {
                        return $match[1];
                    }
                    return $match[1] . '<lb/>';

                },

            ],
            $s
        );
        return $s;
    }

    public static function joinLines(string $s) {
        $s = preg_replace('/\r|\n/', '', $s);
        return $s;
    }

    /**
     * @param string $s
     * @return string|string[]|null
     */
    public static function createXMLTagsFromUncompatibleTags(string $s) {
        $s = preg_replace('/&lt;note place="end"&gt;/i', '<note place="end">', $s);
        $s = preg_replace('/&lt;\/note&gt;/i', '</note>', $s);
        $s = preg_replace('/&lt;/i', '<', $s);
        $s = preg_replace('/&gt;/i', '>', $s);

        return $s;
    }

    /**
     * @param $dom
     * @param $elementName
     * @return mixed
     */
    public static function removeElementsInTag($dom, $str) {
        $xpath = new DOMXPath($dom);
        foreach ($xpath->query($str) as $node) {
            $parent = $node->parentNode;
            while ($node->hasChildNodes()) {
                $parent->insertBefore($node->lastChild, $node->nextSibling);
            }
            $parent->removeChild($node);
        }
        return $dom;
    }

    public static function removeTags($dom, $str) {
        $xpath = new DOMXPath($dom);
        foreach ($xpath->query($str) as $node) {
            $parent = $node->parentNode;
            while ($node->hasChildNodes()) {
                $parent->insertBefore($node->lastChild, $node->nextSibling);
            }
            $parent->removeChild($node);
        }
        return $dom;
    }

    public static function enumerateLBs($dom) {
        $xpath = new DOMXPath($dom);
        $lbCount = 1;
        $abCount = 1;
        $abs = $xpath->query('//ab');
        foreach ($abs as $ab) {
            foreach ($xpath->query('lb', $ab) as $ln) {
                $attr = $dom->createAttribute("n");
                $attr->value = $lbCount;
                $ln->appendChild($attr);
                $lbCount++;
            }
            $lbCount = 1;
        }
    }

    public static function removeControlledVocabsWordTagging($dom) {
        $xpath = new DOMXPath($dom);
        foreach ($xpath->query('//persName/w | //geogName/w | //placeName/w') as $node) {
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
    public static function createComplexSentence(string $s) {
        preg_match_all('/#SB(.|\n)*?#SE/', $s, $matches);
        $match = $matches[0];
        if (!is_null($match) && count($match) != 0) {
            $s = preg_replace('/#SB@([a-z]{3})@([IMF])/', '<s xml:lang="$1" part="$2">', $s);
            $s = preg_replace('/#SB@([a-z]{3})/', '<s xml:lang="$1">', $s);
            $s = preg_replace('/#SB@([IMF])/', '<s part="$1">', $s);
            $s = preg_replace('/#SB/', '<s>', $s);
            $s = preg_replace('/#SE/', '</s>', $s);
        }
        return $s;
    }



    public static function addChildElement($dom, $parent, $child): void {
        $nodes = $dom->getElementsByTagName($parent);
        foreach ($nodes as $node) {
            $node->insertBefore($dom->createElement($child), $node->firstChild);
        }
    }

    public static function addParagraphsBetweenAnonymousBlocks($dom) {
        $abs = $dom->getElementsByTagName("ab");
        foreach ($abs as $ab) {
            try {
                $ab->parentNode->insertBefore($dom->createElement('p'), $ab->nextSibling);
            } catch (Exception $e) {
                $ab->parentNode->appendChild($ab);
            }
        }
    }

    public static function createWords(string $s) {
        $preg = "(\p{Devanagari}|&amp;#x200c;|&amp;#8205;|&amp;x200c;|&amp;8205;)+"; # # is cleaned already
        if (preg_match("/" . $preg . "/u", $s, $matches)) {
            $s = preg_replace('/' . $preg . '/u', '<w>$0</w>', $s);
        }
        return $s;
    }

    /**
     * @param string $s
     * @return string|string[]|null
     */
    public static function handleLineBreakNoWords(string $s) {
        // <w>व<lb break="no" n="2"/>सी</w>
        $s = preg_replace('/<w>(\p{Devanagari}+)<\/w>(\s*<lb\sbreak="no"\sn="\d"\/>\s*)<w>(\p{Devanagari}+)<\/w>/u', '<w>$1$2$3</w>', $s);
        return $s;
    }


    public static function printPHPErrors(): void {
        $errorTypes = E_ALL;
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            echo "[Warning] $errstr $errfile $errline\n";
            return true;
        }, $errorTypes);
    }


    public static function createSurroundWordForChoice(string $s) {
        return preg_replace('/<choice>\s*<sic>.*<\/sic>\s*<corr>.*<\/corr>\s*<\/choice>/','<w>$0</w>', $s);
    }

    /**
     * @param string $s
     * @return string|string[]|null
     */
    public static function removeTagsWithoutContent(string $s) {
        return preg_replace('/<\w+>\s*<\/\w+>/i', ' ', $s);
    }

    /**
     * @param $dom
     * @param $tag
     */
    public static function removeTitleInBody($dom): void {
        $titlesXpath = new DOMXPath($dom);
        $titles = $titlesXpath->query("//body/div/*/title | //div/title");
        foreach ($titles as $title) {
            $title->parentNode->removeChild($title);
        }
    }

    /**
     * @param string $content
     * @return string|string[]|null
     */
    public static function tagReplace(string $content, string $tag, string $replace) {
        return preg_replace('/<' . $tag . '>(.*)<\/' . $tag . '>/', '<' . $replace . '>$1</' . $replace . '>', $content);
    }
}
