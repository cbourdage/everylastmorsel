<?php
$this->startSetup();

$this->run("

DROP TABLE IF EXISTS `plot_crops`;
CREATE TABLE  `plot_crops` (
	`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`crop_id` int(10) unsigned NOT NULL,
	`plot_id` int(10) unsigned NOT NULL,
	`user_id` int(10) unsigned NOT NULL,
	`date_planted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`starting_type` enum('seed', 'seedling', 'plant') NOT NULL DEFAULT 'seed',
	`quantity` smallint unsigned DEFAULT 0,
	`coverage` int unsigned DEFAULT 0,
	`conditions` varchar(255) DEFAULT NULL,
	`yield` varchar(32) DEFAULT NULL,
	`is_for_sale` tinyint DEFAULT 0,
	`is_active` tinyint DEFAULT 1,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY  (`entity_id`),
	CONSTRAINT `fk_plot_crops_plot_id` FOREIGN KEY (`plot_id`) REFERENCES `plot` (`plot_id`) ON DELETE CASCADE,
	CONSTRAINT `fk_plot_crops_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");

$this->endSetup();
