<?php
$this->startSetup();

$this->run("

ALTER TABLE `user` ADD COLUMN `is_confirmed` tinyint DEFAULT 0 AFTER `is_active`;
ALTER TABLE `user` ADD COLUMN `confirmation_key` varchar(255) DEFAULT NULL AFTER `password_hash`;
ALTER TABLE `plot` ADD COLUMN `is_startup` tinyint DEFAULT 0 AFTER `is_new`;

-- update to relation table
DROP TABLE IF EXISTS `user_plot_relationships`;
CREATE TABLE `user_plot_relationships` (
  	`user_id` int(10) unsigned NOT NULL,
  	`plot_id` int(10) unsigned NOT NULL,
  	`role` varchar(24) DEFAULT NULL,
  	PRIMARY KEY (`plot_id`, `user_id`, `role`),
  	CONSTRAINT `fk_relationship_plot_id` FOREIGN KEY (`plot_id`) REFERENCES `plot` (`plot_id`) ON DELETE CASCADE,
  	CONSTRAINT `fk_relationship_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds all links between users and plots';


TRUNCATE `plot`;
TRUNCATE `user`;

ALTER TABLE `plot` AUTO_INCREMENT = 1000;
ALTER TABLE `user` AUTO_INCREMENT = 1000;


--
-- Crops
--
DROP TABLE IF EXISTS `crops`;
CREATE TABLE  `crops` (
	`crop_id` int(10) unsigned NOT NULL auto_increment,
	`name` varchar(255) DEFAULT NULL,
	`latin_name` varchar(32) DEFAULT NULL,
	`type` varchar(32) DEFAULT NULL,
	`categorization` varchar(32) DEFAULT NULL,
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
	`plant_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`starting_type` enum('seed', 'seedling', 'plant') NOT NULL DEFAULT 'seed',
	`number_plants` smallint unsigned DEFAULT 0,
	`coverage` int unsigned DEFAULT 0,
	`condition` varchar(255) DEFAULT NULL,
	`yield` varchar(32) DEFAULT NULL,
	`is_for_sale` tinyint DEFAULT 0,
	`is_active` tinyint DEFAULT 1,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY  (`crop_id`, `plot_id`, `user_id`),
	CONSTRAINT `fk_plot_crops_plot_id` FOREIGN KEY (`plot_id`) REFERENCES `plot` (`plot_id`) ON DELETE CASCADE,
	CONSTRAINT `fk_plot_crops_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");

$this->endSetup();
