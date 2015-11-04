<?php
/**
 * ISO Codes
 * 
 * @author Juan Pedro Gonzalez Gutierrez
 * @copyright Copyright (c) 2015 Juan Pedro Gonzalez Gutierrez
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 */

namespace IsoCodes\Language\Adapter;

interface AdapterInterface
{
    /**
     * Get language name from ISO-639-1 (two-letters code)
     *
     * @param string $code
     * @return array|null
     */
    public function getByCode($code);

    /**
     * Get language name from ISO-639-2/b (three-letter codes) bibliographic
     *
     * @param string $code
     * @return array|null
     */
    public function getByCode2B($code);

    /**
     * Get language name from ISO-639-2/t (three-letter codes) terminologic
     *
     * @param string $code
     * @return array|null
     */
    public function getByCode2T($code);

    /**
     * Get language name from ISO-639-3 (three-letter codes)
     *
     * @param string $code
     * @return array|null
     */
    public function getByCode3($code);
}