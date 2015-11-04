<?php
/**
 * ISO Codes
 * 
 * @author Juan Pedro Gonzalez Gutierrez
 * @copyright Copyright (c) 2015 Juan Pedro Gonzalez Gutierrez
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 */

namespace IsoCodes\Language;

class Language implements LanguageInterface
{
    /**
     * ISO-639-1 (two-letters code)
     * 
     * @var string
     */
    protected $code = null;

    /**
     * ISO-639-2/t (three-letter codes) terminologic
     *
     * @var string
     */
    protected $code2_t = null;

    /**
     * ISO-639-2/b (three-letter codes) bibliographic
     * 
     * @var string
     */
    protected $code2_b = null;

    /**
     * ISO-639-3 (three-letter codes)
     *
     * @var string
     */
    protected $code3 = null;

    /**
     * English name
     *
     * @var string
     */
    protected $name = null;

    /**
     * Native name.
     * 
     * @var string
     */
    protected $native_name = null;

    public function __construct($data = array())
    {
        if (is_array($data)) {
            // TODO: Sanity checks
            $this->code        = isset($data['code']) ? $data['code'] : null;
            $this->code2_t     = isset($data['code2_t']) ? $data['code2_t'] : null;
            $this->code2_b     = isset($data['code2_b']) ? $data['code2_b'] : null;
            $this->code3       = isset($data['code3']) ? $data['code3'] : null;
            $this->name        = isset($data['name']) ? $data['name'] : null;
            $this->native_name = isset($data['native_name']) ? $data['native_name'] : null;
        } elseif ($data instanceof LanguageInterface) {
            $this->code        = $this->getCode();
            $this->code2_t     = $this->getCode2T();
            $this->code2_b     = $this->getCode2B();
            $this->code3       = $this->getCode3();
            $this->name        = $this->getName();
            $this->native_name = $this->getNativeName();
        }
    }

    /**
     * Get ISO-639-1 (two-letters) code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get ISO-639-2/b (three-letter) bibliographic code
     *
     * @return string
     */
    public function getCode2B()
    {
        return $this->code2_b;
    }

    /**
     * Get ISO-639-2/t (three-letter) terminologic code
     *
     * @return string
     */
    public function getCode2T()
    {
        return $this->code2_t;
    }

    /**
     * Get ISO-639-3 (three-letter) code
     *
     * @return string
     */
    public function getCode3()
    {
        return $this->code3;
    }

    /**
     * Get english name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get native name
     * 
     * @return string
     */
    public function getNativeName()
    {
        return $this->native_name;
    }
}