<?php
/**
 * ISO Codes
 * 
 * @author Juan Pedro Gonzalez Gutierrez
 * @copyright Copyright (c) 2015 Juan Pedro Gonzalez Gutierrez
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 */

namespace IsoCodes\Language\Adapter;

/**
 * https://github.com/matriphe/php-iso-639/blob/master/src/ISO639.php
 */
class StaticAdapter
{
    protected $languages = array();

    public function __construct()
    {
        $this->languages = include(dirname(dirname(__DIR__)) . '/data/iso_639.php');
    }

    /**
     * Get language name from ISO-639-1 (two-letters code)
     *
     * @param string $code
     * @return array|null
     */
    public function getByCode($code)
    {
        if (strlen($code) !== 2) {
            return null;
        }

        foreach ($this->languages as $language) {
            if (strcasecmp($code, $language['alpha_2']) === 0) {
                return $language;
            }
        }

        return null;
    }

    /**
     * Get language name from ISO-639-2/b (three-letter codes) bibliographic
     *
     * @param string $code
     * @return array|null
     */
    public function getByCode2B($code)
    {
        if (strlen($code) !== 3) {
            return null;
        }

        foreach ($this->languages as $language) {
            if (strcasecmp($code, $language['alpha_3b']) === 0) {
                return $language;
            }
        }

        return null;
    }

    /**
     * Get language name from ISO-639-2/t (three-letter codes) terminologic
     *
     * @param string $code
     * @return array|null
     */
    public function getByCode2T($code)
    {
        if (strlen($code) !== 3) {
            return null;
        }
        
        foreach ($this->languages as $language) {
            if (strcasecmp($code, $language['alpha_3t']) === 0) {
                return $language;
            }
        }

        return null;
    }
}