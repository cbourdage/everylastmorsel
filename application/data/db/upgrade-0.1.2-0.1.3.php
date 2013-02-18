<?php
$this->startSetup();


$this->run("

ALTER TABLE `yields_purchasable` ADD COLUMN `qty_available` INT(4) NOT NULL AFTER `quantity_unit`;
ALTER TABLE `yields_purchasable` ADD COLUMN `is_for_sale` TINYINT DEFAULT 1 AFTER `quantity_unit`;
ALTER TABLE `yields_purchasable` ADD COLUMN `is_sold_out` TINYINT DEFAULT 0 AFTER `is_for_sale`;

DROP TABLE IF EXISTS `yields_transactions`;
CREATE TABLE `yields_transactions` (
	`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`crop_id` int(10) unsigned NOT NULL,
	`purchasable_id` int(10) unsigned NOT NULL,
	`yield_id` int(10) unsigned NOT NULL,
	`plot_crop_id` int(10) unsigned NOT NULL,
	`quantity` int(4) NOT NULL,
	`quantity_unit` varchar(32) NOT NULL,
	`total` float(4,2) default 0,
	`is_sale` tinyint DEFAULT 1,
	`reason` text DEFAULT '',
	`is_active` tinyint DEFAULT 1,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`entity_id`),
	CONSTRAINT `fk_yields_transaction_crop_id` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`crop_id`) ON DELETE CASCADE,
	CONSTRAINT `fk_yields_transaction_purchasable_id` FOREIGN KEY (`purchasable_id`) REFERENCES `yields_purchasable` (`entity_id`) ON DELETE CASCADE,
	CONSTRAINT `fk_yields_transaction_yield_id` FOREIGN KEY (`yield_id`) REFERENCES `yields` (`yield_id`) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `yields_transactions_link`;
CREATE TABLE `yields_transactions_link` (
	`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`buyer_id` int(10) unsigned NOT NULL,
	`seller_id` int(10) unsigned NOT NULL,
	`transaction_id` int(10) unsigned NOT NULL,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`entity_id`),
	CONSTRAINT `fk_yields_transaction_link_buyer_id` FOREIGN KEY (`buyer_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
	CONSTRAINT `fk_yields_transaction_link_seller_id` FOREIGN KEY (`seller_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
	CONSTRAINT `fk_yields_transaction_link_trans_id` FOREIGN KEY (`transaction_id`) REFERENCES `yields_transactions` (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");


$this->endSetup();
