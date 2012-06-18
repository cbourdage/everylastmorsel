<?php
$this->startSetup();

$this->run("

ALTER TABLE `user` MODIFY COLUMN `gardener_type` ENUM('casual', 'farmer', 'community') NULL AFTER `about`;

-- Add additional table for messages
DROP TABLE IF EXISTS `communication`;
CREATE TABLE `communication` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`parent_id` int(10) unsigned DEFAULT NULL,
  	`to_user_id` int(10) unsigned NOT NULL,
  	`from_user_id` int(10) unsigned NOT NULL,
  	`subject` varchar(24) DEFAULT NULL,
  	`message` text DEFAULT NULL,
  	`is_delivered` tinyint(1) DEFAULT 0,
  	`is_read` tinyint(1) DEFAULT 0,
  	`is_archived` tinyint(1) DEFAULT 0,
  	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  	`modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds all communication requests';

");

$this->endSetup();
