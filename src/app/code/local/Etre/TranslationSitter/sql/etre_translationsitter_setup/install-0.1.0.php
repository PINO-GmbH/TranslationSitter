<?php
/**
 * Copyright (c) 2016 TechDivision GmbH
 * All rights reserved
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see http://www.techdivision.com/
 * To obtain a valid license for using this software please contact us at
 * license@techdivision.com
 */

/**
 * Setup script to create basic table
 *
 * @category   TranslationSitter
 * @package    setup
 * @subpackage sql
 * @copyright  Copyright (c) 2016 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Joachim Havloujian <j.havloujian@techdivision.com>
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$sql = <<<SQL
CREATE TABLE `etre_translate` (
  `key_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Key Id of Translation',
  `string` varchar(255) NOT NULL DEFAULT 'Translate String' COMMENT 'Translation String',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store Id',
  `translate` varchar(255) DEFAULT NULL COMMENT 'Translate',
  `locale` varchar(20) NOT NULL DEFAULT 'en_US' COMMENT 'Locale',
  `crc_string` bigint(20) NOT NULL DEFAULT '1591228201' COMMENT 'Translation String CRC32 Hash',
  `is_from_translationsitter` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Translation provided by Etre_TranslationSitter',
  `translationsitter_source` text NOT NULL COMMENT 'Where did this translation come from?',
  PRIMARY KEY (`key_id`),
  UNIQUE KEY `UNQ_CORE_TRANSLATE_STORE_ID_LOCALE_CRC_STRING_STRING` (`store_id`,`locale`,`crc_string`,`string`),
  KEY `IDX_CORE_TRANSLATE_STORE_ID` (`store_id`),
  CONSTRAINT `etre_translate_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Translations';
SQL;


$installer->run($sql);

$installer->endSetup();