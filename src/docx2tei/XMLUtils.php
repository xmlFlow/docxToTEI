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
                        print_error("[Error]  " . $parts[$i] . " doese not conatin a = sign");
                    }
                }
            }
            $s = str_replace($matches[0], $gap->ownerDocument->saveXML($gap), $s);
        }
        return $s;

    }

    /**
     * @param string $str
     * @param string $xml
     * @return string
     */
    public static function createExtraAtrributes(string $str, string $xml, int $offset): string {
        $parts = array_slice(explode("@", $str), $offset);
        foreach ($parts as $part) {
            list ($key, $value) = explode('=', $part);
            if (!is_null($key) && !is_null($value)) {
                $xml .= '@' . $key . '=' . $value;
            }
        }
        return $xml;
    }


}