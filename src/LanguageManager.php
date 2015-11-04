<?php
/**
 * ISO Codes
 * 
 * @author Juan Pedro Gonzalez Gutierrez
 * @copyright Copyright (c) 2015 Juan Pedro Gonzalez Gutierrez
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 */

namespace IsoCodes\Language;

use IsoCodes\Language\Adapter\AdapterInterface;
use IsoCodes\Language\Adapter\StaticAdapter;

class LanguageManager
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    public function __construct(AdapterInterface $adapter = null)
    {
        if (null === $adapter) {
            $adapter = new StaticAdapter();
        }

        $this->adapter = $adapter;
    }

    /**
     * Get language name from ISO-639-1 (two-letters code)
     *
     * @param string $code
     * @return string
     */
    public function getByCode($code)
    {
        $lang = $this->getAdapter()->getByCode($code);
        if (null !== $lang) {
            return new Language($lang);
        }
        return null;
    }

    /**
     * Get language name from ISO-639-2/b (three-letter codes) bibliographic
     *
     * @param string $code
     * @return string
     */
    public function getByCode2B($code)
    {
        $lang = $this->getAdapter()->getByCode2B($code);
        if (null !== $lang) {
            return new Language($lang);
        }
        return null;
    }

    /**
     * Get language name from ISO-639-2/t (three-letter codes) terminologic
     *
     * @param string $code
     * @return string
     */
    public function getByCode2T($code)
    {
        $lang = $this->getAdapter()->getByCode2T($code);
        if (null !== $lang) {
            return new Language($lang);
        }
        return null;
    }

    /**
     * Get language name from ISO-639-3 (three-letter codes)
     *
     * @param string $code
     * @return string
     */
    public function getByCode3($code)
    {
        $lang = $this->getAdapter()->getByCode3($code);
        if (null !== $lang) {
            return new Language($lang);
        }
        return null;
    }

    /**
     * Get the languages adater.
     * 
     * @return AdapterInterface|null
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Set the languages adapter.
     * 
     * @param AdapterInterface $adapter
     * @return self
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }
}