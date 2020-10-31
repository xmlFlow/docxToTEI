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
}