<?php

class Etre_TranslationSitter_Model_Google
{
    /** @var  string $apiKey */
    protected $apiKey;
    protected $sourceLanguage = "en";
    protected $locale;
    protected $storeId;
    protected $apiBaseUrl     = "https://www.googleapis.com/language/translate/v2";

    public function __construct()
    {
        $this->setLocale(Mage::app()->getLocale()->getLocaleCode());
        $this->setStoreId(Mage::app()->getStore()->getId());
//        /dd(Mage::getStoreConfig('system/translationsitter/googleApiKey'));
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function process($text, $code)
    {
        if ($this->logAreaEnabled()):
            if ($this->autoGoogleTranslateEnabled()):
                return $this->getTranslation($text, $code);
            else:
                $this->logTranslation($code, $text);
            endif;

            return $text;
        else:
            return $text;
        endif;
    }

    public function logAreaEnabled()
    {
        $isAdmin    = Mage::app()->getStore()->isAdmin();
        $isFrontend = !$isAdmin;
        $helper     = Mage::helper("etre_translationsitter");
        if ($isAdmin && $helper->getLogAdminEnabled()) {
            return true;
        }
        if ($isFrontend && $helper->getLogFrontendEnabled()) {
            return true;
        }
        return false;
    }

    public function autoGoogleTranslateEnabled()
    {
        $isAdmin    = Mage::app()->getStore()->isAdmin();
        $isFrontend = !$isAdmin;
        $helper     = Mage::helper("etre_translationsitter");
        if ($isAdmin && $helper->getAutoTranslateAdminEnabled()) {
            return true;
        }
        if ($isFrontend && $helper->getAutoTranslateFrontEnabled()) {
            return true;
        }
        return false;
    }

    public function getTranslation($text, $code)
    {
        try {
            $translated = $this->getApiTranslation($text, $code);
            if (isset($translated)):
                $this->logTranslation($code, $translated, "Translation Sitter: Google API");
            else:
                throw new Exception(
                    $this->__("There was a problem getting the translation from Google: {$translated}")
                );
            endif;
        } catch (Exception $e) {
            $integrityConstraintCode           = 23000;
            $isNotIntegrityConstraintViolation = $e->getCode() !== $integrityConstraintCode;
            if ($isNotIntegrityConstraintViolation):
                /** Magento will not know that new translations have been inserted until the next page load.
                 * We can expect integrity constraint violations because of this. When that happens, let's just keep going */
                $translated = $text;
            else:
                Mage::logException($e);
                $translated = $text;
            endif;
        }
        return $translated;
    }

    protected function getApiTranslation($text = "")
    {

        $stringToTranslate   = urlencode($text);
        $destinationLanguage = strtok($this->getLocale(), "_");
        $request             = new Zend_Http_Client();
        $uri                 =
            "{$this->apiBaseUrl}?key={$this->apiKey}&q={$stringToTranslate}&source={$this->sourceLanguage}&target={$destinationLanguage}";
        $request
            ->setUri($uri)
            ->request("GET");
        $googleResponse    = $request->getLastResponse()->getBody();
        $googleTranslation = json_decode($googleResponse);
        if (is_object($googleTranslation)):
            if (!$googleTranslation->error):
                return $translated = $googleTranslation->data->translations[0]->translatedText;
            endif;
        endif;
        return false;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @param $string
     * @param $source
     * @param $translated
     */
    protected function logTranslation($string, $translated, $source = "Translation Sitter Log")
    {
        /** @var Etre_TranslationSitter_Model_Translations $translationSitter */
        $translationSitter = Mage::getModel("etre_translationsitter/translations");
        $uniqueIndex       = [
            'store_id'   => $this->getStoreId(),
            'string'     => $string,
            'crc_string' => crc32($string),
            'locale'     => $this->getLocale(),
        ];
        $translationSitter->loadByUniqueIndex($uniqueIndex);
        $translationSitter->setData('translationsitter_source', $source);
        $translationSitter->setData('translate', $translated);

        try {
            $translationSitter->save();
        } catch (Exception $e) {
            /*
             * To prevent constraint violations that stem from race-conditions (aka prevent duplicate entries as
             * expected) from flooding our exception.log, we only log non-constraint-violation exception
             */
            if (!$this->_isConstraintViolationException($e)) {
                Mage::logException($e);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param mixed $storeId
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * Returns whether an exception indicates that a constraint was violated.
     *
     * @param Exception $e
     * @return bool
     */
    protected function _isConstraintViolationException(Exception $e)
    {
        $isCcontraintViolation = false;
        $message               = $e->getMessage();

        switch (true) {
            case (strpos($message, 'constraint violation') !== false):
                $isCcontraintViolation = true;
                break;
        }

        return $isCcontraintViolation;
    }

}