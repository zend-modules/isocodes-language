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
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\I18n\Translator\TranslatorInterface;

class LanguageManager implements TranslatorAwareInterface
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Translator used for Language names.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Whether the translator is enabled.
     *
     * @var bool
     */
    protected $translatorEnabled = true;

    /**
     * Translator text domain to use.
     *
     * @var string
     */
    protected $translatorTextDomain = 'language';

    public function __construct(AdapterInterface $adapter = null)
    {
        if (null === $adapter) {
            $adapter = new StaticAdapter();
        }

        $this->adapter = $adapter;

        // Translator
        $this->translator = new Translator();
        $this->translator->setLocale('en');
        $this->translator->setFallbackLocale('en');
        $this->translator->addTranslationFilePattern('gettext', dirname(__DIR__) . '/language/', '%s.mo', $this->translatorTextDomain);
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
            if ($this->isTranslatorEnabled()) {
                if (isset($lang['name'])) {
                    $lang['name'] = $this->translator->translate($lang['name'], $this->translatorTextDomain);
                }
            }
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
            if ($this->isTranslatorEnabled()) {
                if (isset($lang['name'])) {
                    $lang['name'] = $this->translator->translate($lang['name'], $this->translatorTextDomain);
                }
            }
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
            if ($this->isTranslatorEnabled()) {
                if (isset($lang['name'])) {
                    $lang['name'] = $this->translator->translate($lang['name'], $this->translatorTextDomain);
                }
            }
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

    /**
     * Sets translator to use in helper
     *
     * @param  TranslatorInterface $translator  [optional] translator.
     *                                           Default is null, which sets no translator.
     * @param  string              $textDomain  [optional] text domain
     *                                           Default is null, which skips setTranslatorTextDomain
     * @return CountryManager
     */
    public function setTranslator(TranslatorInterface $translator = null, $textDomain = null)
    {
        $this->translator = $translator;
        if ($textDomain !== null) {
            $this->setTranslatorTextDomain($textDomain);
        }
        return $this;
    }

    /**
     * Returns translator used in object
     *
     * @return TranslatorInterface|null
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Checks if the object has a translator
     *
     * @return bool
     */
    public function hasTranslator()
    {
        return $this->translator !== null;
    }

    /**
     * Sets whether translator is enabled and should be used
     *
     * @param  bool $enabled [optional] whether translator should be used.
     *                       Default is true.
     * @return CountryManager
     */
    public function setTranslatorEnabled($enabled = true)
    {
        $this->translatorEnabled = $enabled;
        return $this;
    }

    /**
     * Returns whether translator is enabled and should be used
     *
     * @return bool
     */
    public function isTranslatorEnabled()
    {
        if ($this->hasTranslator()) {
            return $this->translatorEnabled;
        }
        return false;
    }

    /**
     * Set translation text domain
     *
     * @param  string $textDomain
     * @return CountryManager
     */
    public function setTranslatorTextDomain($textDomain = 'language')
    {
        $this->translatorTextDomain = $textDomain;
        return $this;
    }

    /**
     * Return the translation text domain
     *
     * @return string
     */
    public function getTranslatorTextDomain()
    {
        return $this->translatorTextDomain;
    }
}