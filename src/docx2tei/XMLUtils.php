<?php


namespace docx2tei;


class XMLUtils {

    protected static $BOUNDARY = '#';

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
    public static function illegibleGaps(string $s) {

        preg_match_all('/' . '' . XMLUtils::$BOUNDARY . '(\+)+([\@][((\w|=)>\s)]*)*' . XMLUtils::$BOUNDARY . '/i', $s, $matches);
        $gap = $matches[0];
        if (!is_null($gap) && count($gap) != 0) {
            $str = str_replace(XMLUtils::$BOUNDARY, '', $gap[0]);
            $elem =  new \DOMDocument();
            $gap = $elem->createElement("gap");
            $gapsLength = 0;
            $parts = explode("@", $str);
            if (!is_null($parts)) {
                $gapsLength = count_chars(array_pop($parts));
            };
            if (!is_null($parts)) {
                $ex = $elem->createAttribute('extent');
                $type = ($gapsLength==1) ? 'character' : 'characters';
                $extent = array_pop($parts);
                if (is_null($extent))  $extent = $type;
                $extent->value = $gapsLength.' '.$extent;
                $gap->appendChild($ex);

            };
            if (!is_null($parts)) {
                $agent = array_pop($parts);
                if (!is_null($agent)){

                }
            };


            foreach ($parts as $part) {

            }


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