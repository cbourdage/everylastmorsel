<?php
$this->startSetup();


$this->run("

DROP TABLE IF EXISTS `temp_crops`;

DROP TABLE IF EXISTS `yields_purchasable`;
CREATE TABLE  `yields_purchasable` (
	`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`yield_id` int(10) unsigned NOT NULL,
	`plot_crop_id` int(10) unsigned NOT NULL,
	`quantity` int(10) unsigned NOT NULL,
	`price` float(4,2) DEFAULT 0.00,
	`is_active` tinyint DEFAULT 1,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`entity_id`),
	CONSTRAINT `fk_yields_purchasable_yield_id` FOREIGN KEY (`yield_id`) REFERENCES `yields` (`yield_id`) ON DELETE CASCADE,
	CONSTRAINT `fk_yields_purchasable_plot_crop_id` FOREIGN KEY (`plot_crop_id`) REFERENCES `plot_crops` (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");

$this->endSetup();
