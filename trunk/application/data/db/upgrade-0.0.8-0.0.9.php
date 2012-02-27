<?php
$this->startSetup();

$this->run("

ALTER TABLE `user` ADD COLUMN `image` varchar(255) NOT NULL AFTER `location`;

--
-- Crops
 --
DROP TABLE IF EXISTS `crops`;
CREATE TABLE  `crops` (
	`crop_id` int(10) unsigned NOT NULL auto_increment,
	`name` varchar(255) DEFAULT NULL,
	`type` varchar(32) DEFAULT NULL,
	`size` varchar(32) DEFAULT NULL,
	`season` varchar(32) DEFAULT NULL,
	`information` varchar(255) DEFAULT NULL,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY  (`crop_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Crops to plots relationships
--
DROP TABLE IF EXISTS `plot_crops`;
CREATE TABLE  `plot_crops` (
	`crop_id` int(10) unsigned NOT NULL,
	`plot_id` int(10) unsigned NOT NULL,
	`user_id` int(10) unsigned NOT NULL,
	`yield` varchar(32) DEFAULT NULL,
	`is_active` TINYINT DEFAULT 1,
	`for_sale` TINYINT DEFAULT 0,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY  (`crop_id`, `plot_id`, `user_id`),
	CONSTRAINT `fk_plot_crops_plot_id` FOREIGN KEY (`plot_id`) REFERENCES `plot` (`plot_id`) ON DELETE CASCADE,
	CONSTRAINT `fk_plot_crops_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");

$this->endSetup();
