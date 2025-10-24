<?php
namespace PPLShipping\tmodule;

use Db;

trait TInstallDb {
    public function installDB() {
        $prefix = _DB_PREFIX_;
        $engine = _MYSQL_ENGINE_;

        $queries = <<<MULTILINE
CREATE TABLE IF NOT EXISTS `{$prefix}ppl_log` (
  `id_ppl_log` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime,
  `message` text NOT NULL,
  `errorhash` varchar(128) NOT NULL,
  PRIMARY KEY (`id_ppl_log`),
  UNIQUE KEY `errorhas` (`errorhash`)
) ENGINE=$engine DEFAULT CHARSET=utf8mb4;
---split---
CREATE TABLE IF NOT EXISTS `{$prefix}ppl_parcel` (
  `id_ppl_parcel` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `name2` varchar(100) DEFAULT NULL,
  `street` varchar(50) NOT NULL,
  `street2` varchar(50) DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `code` varchar(20) NOT NULL,
  `remote_id` varchar(20) NOT NULL,
  `country` varchar(2) NOT NULL,
  `lat` float NOT NULL,
  `lng` float NOT NULL,
  PRIMARY KEY (`id_ppl_parcel`),
  UNIQUE KEY `remoteId` (`remote_id`)
) ENGINE=$engine DEFAULT CHARSET=utf8;
---split---
CREATE TABLE IF NOT EXISTS `{$prefix}ppl_cart` (
  `id_ppl_cart` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_cart` bigint(20) NOT NULL,
  `id_ppl_parcel`  bigint(20) NOT NULL,
  PRIMARY KEY (`id_ppl_cart`)
) ENGINE=$engine DEFAULT CHARSET=utf8;
---split---
CREATE TABLE IF NOT EXISTS `{$prefix}ppl_order` (
  `id_ppl_order` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_order` bigint(20) NOT NULL,
  `id_ppl_parcel`  bigint(20) NOT NULL,
  PRIMARY KEY (`id_ppl_order`)
) ENGINE=$engine DEFAULT CHARSET=utf8;
---split---
CREATE TABLE IF NOT EXISTS `{$prefix}ppl_address` (
  `id_ppl_address` bigint(20) NOT NULL AUTO_INCREMENT,
  `address_name` varchar(40) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `mail` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `street` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `type` varchar(10) NOT NULL,
  `note` varchar(300) DEFAULT NULL,
  `hidden` tinyint(4) NOT NULL,
  `lock` tinyint(4) NOT NULL,
  PRIMARY KEY (`id_ppl_address`)
) ENGINE=$engine DEFAULT CHARSET=utf8mb4;
---split---
CREATE TABLE IF NOT EXISTS `{$prefix}ppl_package` (
  `id_ppl_package` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_ppl_shipment` bigint(20) DEFAULT NULL,
  `reference_id` varchar(40) DEFAULT NULL,
  `phase` varchar(20) NOT NULL,
  `phase_label` varchar(80) DEFAULT NULL,
  `last_update_phase` datetime DEFAULT NULL,
  `last_test_phase` datetime DEFAULT NULL,
  `ignore_phase` tinyint(4) DEFAULT NULL,
  `shipment_number` varchar(40) DEFAULT NULL,
  `weight` decimal(10,0) DEFAULT NULL,
  `insurance` decimal(10,0) DEFAULT NULL,
  `import_error` text DEFAULT NULL,
  `import_error_code` text DEFAULT NULL,
  `label_id` text DEFAULT NULL,
  `lock` tinyint(4) NOT NULL,
  `status` int(11) DEFAULT NULL,
  `status_label` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id_ppl_package`)
) ENGINE=$engine DEFAULT CHARSET=utf8mb4;
---split---
CREATE TABLE IF NOT EXISTS `{$prefix}ppl_shipment` (
  `id_ppl_shipment` int(11) NOT NULL AUTO_INCREMENT,
  `id_order` int(11) DEFAULT NULL,
  `import_errors` text DEFAULT NULL,
  `import_state` varchar(20) NOT NULL,
  `service_code` varchar(20) DEFAULT NULL,
  `service_name` varchar(40) DEFAULT NULL,
  `reference_id` varchar(50) NOT NULL,
  `id_recipient_address` int(11) DEFAULT NULL,
  `id_sender_address` int(11) DEFAULT NULL,
  `cod_value` decimal(10,0) DEFAULT NULL,
  `cod_value_currency` varchar(4) DEFAULT NULL,
  `cod_variable_number` varchar(10) DEFAULT NULL,
  `has_parcel` tinyint(4) NOT NULL,
  `id_parcel` int(11) DEFAULT NULL,
  `batch_id` varchar(50) DEFAULT NULL,
  `batch_label_group` datetime DEFAULT NULL,
  `note` varchar(300) DEFAULT NULL,
  `age` varchar(3) DEFAULT NULL,
  `package_ids` text DEFAULT NULL,
  `lock` tinyint(4) NOT NULL,
  PRIMARY KEY (`id_ppl_shipment`),
  UNIQUE KEY `reference_id` (`reference_id`)
) ENGINE=$engine DEFAULT CHARSET=utf8mb4;
---split---
CREATE TABLE IF NOT EXISTS `{$prefix}ppl_collection` (
  `id_ppl_collection` bigint(20) NOT NULL AUTO_INCREMENT,
  `remote_collection_id` varchar(80) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `send_date` datetime NOT NULL,
  `send_to_api_date` datetime DEFAULT NULL,
  `reference_id` varchar(50) NOT NULL,
  `state` varchar(20) DEFAULT NULL,
  `shipment_count` int(11) NOT NULL,
  `estimated_shipment_count` int(11) NOT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `note` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_ppl_collection`),
  UNIQUE KEY `reference_id` (`reference_id`)
) ENGINE=$engine DEFAULT CHARSET=utf8mb4;
---split---
CREATE TABLE IF NOT EXISTS `{$prefix}ppl_base_disabled_rule` (
  `id_base_disabled_rule` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) DEFAULT NULL,
  `id_category` int(11) DEFAULT NULL,
  `disabled_parcelshop`  tinyint(4),
  `disabled_parcelbox` tinyint(4),
  `disabled_alzabox` tinyint(4),
  `disabled_methods` text,
  `required_age18` tinyint(4),
  `required_age15` tinyint(4),
  PRIMARY KEY (`id_base_disabled_rule`),
  UNIQUE KEY `id_product` (`id_product`),
  UNIQUE KEY `id_category` (`id_category`)
) ENGINE=$engine DEFAULT CHARSET=utf8mb4;
---split---
CREATE TABLE IF NOT EXISTS `{$prefix}ppl_batch` (
  `id_ppl_batch` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb3_bin DEFAULT NULL,
  `remote_batch_id` varchar(50) COLLATE utf8mb3_bin DEFAULT NULL,
  `lock` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id_ppl_batch`)
) ENGINE=$engine DEFAULT CHARSET=utf8mb4;
MULTILINE;

        $success = true;

        foreach (explode("---split---", $queries) as $query) {
            $success  =  $success && Db::getInstance()->execute($query);
        }

        $sql = "SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = '" . pSQL( "{$prefix}ppl_shipment") . "' 
        AND TABLE_SCHEMA = '" . _DB_NAME_ . "' 
        AND COLUMN_NAME = '" . pSQL( "print_state") . "'";

        $result = Db::getInstance()->getValue($sql);
        if (!$result)
        {
            Db::getInstance()->execute("ALTER TABLE `{$prefix}ppl_shipment` ADD COLUMN `print_state` varchar(20) NULL ;");
        }

        $sql = "SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = '" . pSQL( "{$prefix}ppl_shipment") . "' 
        AND TABLE_SCHEMA = '" . _DB_NAME_ . "' 
        AND COLUMN_NAME = '" . pSQL( "id_batch_local") . "'";

        $result = Db::getInstance()->getValue($sql);
        if (!$result)
        {
            Db::getInstance()->execute("ALTER TABLE `{$prefix}ppl_shipment` ADD COLUMN `id_batch_local` int(11) NULL ;");
        }

        $sql = "SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = '" . pSQL( "{$prefix}ppl_shipment")  . "' ".
               " AND TABLE_NAME = '" . pSQL("{$prefix}ppl_shipment") . "' AND INDEX_NAME = '" . pSQL("reference_id") . "'";
        
        $result = Db::getInstance()->getValue($sql);

        if ($result)
        {
            Db::getInstance()->execute("ALTER TABLE `{$prefix}ppl_shipment` DROP INDEX `reference_id`;");
        }


        return $success;
    }
}