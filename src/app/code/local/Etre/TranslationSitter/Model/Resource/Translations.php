<?php

class Etre_TranslationSitter_Model_Resource_Translations extends Mage_Core_Model_Resource_Db_Abstract{

    protected function _construct()
    {
        $this->_init('etre_translationsitter/translations', 'key_id');
    }

    /**
     * @param Etre_TranslationSitter_Model_Translations $object
     * @param array $uniqueIndex
     * @return $this
     */
    public function loadByUniqueIndex(Etre_TranslationSitter_Model_Translations $object, array $uniqueIndex)
    {
        $read = $this->_getReadAdapter();

        if ($read && $uniqueIndex) {
            $select = $this->_getLoadByUniqueKeySelect($uniqueIndex);
            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            } else {
                $object->addData($uniqueIndex);
            }
        }

        $this->unserializeFields($object);
        $this->_afterLoad($object);

        return $this;
    }

    protected function _getLoadByUniqueKeySelect($uniqueConstraint)
    {
        $read = $this->_getReadAdapter();
        $table = $this->getMainTable();

        $select = $read->select()
            ->from($table);

        foreach ($uniqueConstraint as $column => $value) {
            $field  = $read->quoteIdentifier(sprintf('%s.%s',$table, $column));
            $select->where($field . '=?', $value);
        }

        return $select;
    }
}