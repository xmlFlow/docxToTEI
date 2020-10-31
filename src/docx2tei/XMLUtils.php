<?php


namespace docx2tei;


class XMLUtils {
    public function __construct(){

    }
    /**
     * @param $s
     * @return string|string[]|null
     */
    public static function clean(string $s) {
        return preg_replace('/\s+/i', ' ', $s);
    }

    /**
     * @param $s
     * @return string|string[]|null
     */
    public static function createComplexSentence(string $s) {
        $s= preg_replace('/\#SB/', '<s>', $s);
        $s= preg_replace('/\#SE/', '</s>', $s);
        return $s;
    }
    /**
     * @param $s
     * @return string|string[]|null
     */
    public static function createLineBegin(string $s) {
        $s= preg_replace('/<p>(\s)*<\/p>/i', '<lb/>', $s);
        return $s;
    }
    public static function createLineBeginNoBreak(string $s) {
        $s= preg_replace('/<p>(\s)*-(\s)*<\/p>/i', '<lb break="no"/>', $s);
        return $s;
    }
}