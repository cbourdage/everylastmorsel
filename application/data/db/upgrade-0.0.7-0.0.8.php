<?php
$this->startSetup();

$this->run("

DROP TABLE IF EXISTS `plot_status_updates`;

--
-- Definition of table `plot_status_updates`
--
CREATE TABLE  `plot_status_updates` (
	`update_id` int(10) unsigned NOT NULL auto_increment,
	`plot_id` int(10) unsigned NOT NULL,
	`user_id` int(10) unsigned NOT NULL,
	`parent_id` int(10) unsigned DEFAULT NULL,
	`type` enum('text', 'image', 'crop', 'link') DEFAULT 'text',
	`title` varchar(255) DEFAULT NULL,
	`content` longtext DEFAULT NULL,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY  (`update_id`),
	KEY `parent_id` (`parent_id`),
	CONSTRAINT `fk_plot_status_id` FOREIGN KEY (`plot_id`) REFERENCES `plot` (`plot_id`) ON DELETE CASCADE,
	CONSTRAINT `fk_plot_status_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


");

$this->endSetup();
