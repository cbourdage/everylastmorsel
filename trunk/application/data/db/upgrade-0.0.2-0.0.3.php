<?php
$this->startSetup();

$this->run("

DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `id` varchar(255) NOT NULL DEFAULT '',
  `modified` int(10) unsigned NOT NULL DEFAULT '0',
  `lifetime` int(10) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Session data store';

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

-- Add additional table for messages
DROP TABLE IF EXISTS `communication`;
CREATE TABLE `communication` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`parent_id` int(10) unsigned DEFAULT NULL,
  	`user_to_id` int(10) unsigned NOT NULL,
  	`user_from_id` int(10) unsigned NOT NULL,
  	`subject` varchar(24) DEFAULT NULL,
  	`message` text DEFAULT NULL,
  	`delivered` tinyint(1) DEFAULT 0,
  	`read` tinyint(1) DEFAULT 0,
  	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  	`modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds all communication requests';

");

$this->endSetup();
