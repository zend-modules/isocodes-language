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
    protected $alpha_2 = null;

    /**
     * ISO-639-2/t (three-letter codes) terminologic
     *
     * @var string
     */
    protected $alpha_3t = null;

    /**
     * ISO-639-2/b (three-letter codes) bibliographic
     * 
     * @var string
     */
    protected $alpha_3b = null;

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
            $this->alpha_2     = isset($data['alpha_2']) ? $data['code'] : null;
            $this->alpha_3t    = isset($data['alpha_3t']) ? $data['code2_t'] : null;
            $this->alpha_3b    = isset($data['alpha_3b']) ? $data['code2_b'] : null;
            $this->name        = isset($data['name']) ? $data['name'] : null;
            $this->native_name = isset($data['native_name']) ? $data['native_name'] : null;
        } elseif ($data instanceof LanguageInterface) {
            $this->alpha_2     = $this->getCode();
            $this->alpha_3t    = $this->getCode2T();
            $this->alpha_3b    = $this->getCode2B();
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
     * Get english name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the language name.
     * 
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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