<?php


namespace docx2tei;


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
     * @param $s
     * @return string|string[]|null
     */
    public static function createComplexSentence(string $s) {
        $s = preg_replace('/\#SB/', '<s>', $s);
        $s = preg_replace('/\#SE/', '</s>', $s);
        return $s;
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

    /**
     * @param string $s
     * @return string|string[]|null
     */
    public static function joinLines(string $s) {
        $s = preg_replace('/\r|\n/', '', $s);
        return $s;
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
            if (count($parts) == 3) $suffix2 = str_replace('}', '', $parts[2]);
            $prefix = explode('@', $parts[0]);

            $tagName = $prefix[0];
            $tagName = self::removeUnnecessaryChars($tagName);

            $elem = new \DOMDocument();


            foreach ($tags as $tag) {
                if ($tagName == $tag["original"]) {
                    $tagName = str_replace($tag ["original"], $tag["replace"], $tagName);
                    $tagElem = $elem->createElement($tagName,$suffix1);
                    // remove tag from array
                    array_shift($prefix);
                    for ($i = 0; $i < count($tag["attributes"]); $i++) {
                        if ($i < count($tag["attributes"])) {
                            $attr = $elem->createAttribute($tag["attributes"][$i]['tag']);
                            $attr->value = $tag["attributes"][$i]['default'];
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
                    $s = str_replace($matches[0], $tagElem->ownerDocument->saveXML($tagElem), $s);
                }
            }
        }
        return $s;
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

    static function print_error($message): void {
        echo("[XML Parsing error]" . $message . "\n");
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
            $elem = new \DOMDocument();
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
            $elem = new \DOMDocument();
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


            };
            if (count($parts) > 0) {
                $agent = array_shift($parts);
                if (!is_null($agent)) {
                    $ag = $elem->createAttribute('agent');
                    $ag->value = $agent;
                    $gap->appendChild($ag);

                }
            };
            if (count($parts) > 0) {
                for ($i = 0; $i < count($parts); $i++) {
                    $extras = explode('=', $parts[$i]);
                    if (count($extras) == 2) {
                        $attr = $elem->createAttribute($extras[0]);
                        $attr->value = $extras[1];
                        $gap->appendChild($attr);
                    } else {
                        self::print_error($parts[$i] . " does not conatin a = sign");
                    }
                }
            }
            $s = str_replace($matches[0], $gap->ownerDocument->saveXML($gap), $s);
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
                    array("tag" => "reason", "default" => "lost"),
                )
            ),

        ];
        return $tags;
    }


}