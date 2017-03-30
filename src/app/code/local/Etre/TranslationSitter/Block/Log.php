<?php

/**
 * Created by PhpStorm.
 * User: tyler
 * Date: 11/5/16
 * Time: 1:27 PM
 */
class Etre_TranslationSitter_Block_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'etre_translationsitter';
        $this->_controller = 'log';
        $this->_headerText = $this->__('Translation Overrides');
        //$this->_addButtonLabel  = $this->__('Import');

        $this->addButton("export-csv", [
            'label'   => $this->__('Export CSV'),
            'onclick' => "setLocation('{$this->getExportUrlByFormat("csv")}')",
            'class'   => $this->__('export'),
        ]);

        $this->addButton("export-xml", [
            'label'   => $this->__('Export XML'),
            'onclick' => "setLocation('{$this->getExportUrlByFormat("xml")}')",
            'class'   => $this->__('export'),
        ]);

        parent::__construct();
        $this->_removeButton('add');
    }

    /**
     * @param $format string xml|csv
     * @return string
     */
    public function getExportUrlByFormat($format)
    {
        return $this->getUrl('*/*/export', ['format' => $format]);
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }
    public function getImportUrl()
    {
        return $this->getUrl('*/*/import');
    }


}

