<?php

/**
 * Class Etre_TranslationSitter_Model_Translations
 *
 * @method Etre_TranslationSitter_Model_Resource_Translations getResource
 */
class Etre_TranslationSitter_Model_Translations extends Mage_Core_Model_Abstract {

    protected function _construct()
    {
        $this->_init('etre_translationsitter/translations');
    }

    /**
     * Loads a model by its unique index
     *
     * example
     * [
     *     'string' => 'to be translated',
     *     'locale' => 'en_GB',
     *     'crc_string' => crc32('to be translated'),
     * ]
     *
     * @param array $uniqueIndex
     */
    public function loadByUniqueIndex(array $uniqueIndex)
    {
        $this->getResource()->loadByUniqueIndex($this, $uniqueIndex);
    }
}