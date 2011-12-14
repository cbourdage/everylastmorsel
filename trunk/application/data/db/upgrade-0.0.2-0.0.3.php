<?php
$this->startSetup();

$this->run("
-- update to relation table
DROP TABLE IF EXISTS `user_plot_relationships`;
CREATE TABLE `user_plot_relationship` (
  	`user_id` int(10) unsigned NOT NULL,
  	`plot_id` int(10) unsigned NOT NULL,
  	`role` varchar(24) DEFAULT NULL,
  	PRIMARY KEY (`plot_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds all links between users and plots';

-- Add additional table for messages
DROP TABLE IF EXISTS `communication`;
CREATE TABLE `communication` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  	`user_to_id` int(10) unsigned NOT NULL,
  	`user_from_id` int(10) unsigned NOT NULL,
  	`subject` varchar(24) DEFAULT NULL,
  	`message` text DEFAULT NULL,
  	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  	`modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds all communication requests';
");

$this->endSetup();
