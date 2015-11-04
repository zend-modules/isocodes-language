<?php 
/**
 * ISO Codes
 * 
 * @author Juan Pedro Gonzalez Gutierrez
 * @copyright Copyright (c) 2015 Juan Pedro Gonzalez Gutierrez
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 */
 
 namespace IsoCodes\Language;
 
 interface LanguageInterface
 {
    /**
     * Get ISO-639-1 (two-letters) code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get ISO-639-2/b (three-letter) bibliographic code
     *
     * @return string
     */
    public function getCode2B();

    /**
     * Get ISO-639-2/t (three-letter) terminologic code
     *
     * @return string
     */
    public function getCode2T();

    /**
     * Get ISO-639-3 (three-letter) code
     *
     * @return string
     */
    public function getCode3();

    /**
     * Get english name
     * 
     * @return string
     */
    public function getName();

    /**
     * Get native name
     * 
     * @return string
     */
    public function getNativeName();
}