<?php
$this->startSetup();

$this->run("

DROP TABLE IF EXISTS `early_signups`;
CREATE TABLE  `early_signups` (
	`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`email` varchar(128) NOT NULL,
	`region` varchar(128) NOT NULL,
	`ip_address` int unsigned NOT NULL,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `geolocation_cache`;
CREATE TABLE  `geolocation_cache` (
	`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`latitude` decimal(10,8) NOT NULL,
  	`longitude` decimal(10,8) NOT NULL,
	`address` varchar(128) DEFAULT '',
	`city` varchar(128) DEFAULT '',
	`state` varchar(128) DEFAULT '',
	`zipcode` char(7) DEFAULT NULL,
	`county` varchar(128) DEFAULT '',
	`raw` text DEFAULT '',
	`ip_address` int unsigned NOT NULL,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `logger_event`;
CREATE TABLE  `logger_event` (
	`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned DEFAULT NULL,
	`action` varchar(128) DEFAULT '',
	`url` varchar(128) DEFAULT '',
	`note` varchar(128) DEFAULT '',
	`ip_address` int unsigned NOT NULL,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");

$this->endSetup();
