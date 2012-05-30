<?php
$this->startSetup();

$this->run("

DROP TABLE IF EXISTS `newsletter`;
CREATE TABLE  `newsletter` (
	`newsletter_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`email` varchar(128) NOT NULL,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY  (`newsletter_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");

$this->endSetup();
