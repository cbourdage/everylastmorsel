<?php
$this->startSetup();

$this->run("

-- create images
-- Add additional table for messages
DROP TABLE IF EXISTS `plot_images`;
CREATE TABLE `plot_images` (
	`image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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

-- create status messages

");

$this->endSetup();
