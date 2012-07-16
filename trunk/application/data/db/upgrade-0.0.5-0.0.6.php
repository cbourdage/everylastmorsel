<?php
$this->startSetup();

$this->run("

ALTER TABLE `plot` AUTO_INCREMENT = 1000;

--
-- Definition of table `plot_images`
--

CREATE TABLE  `plot_images` (
	`image_id` int(10) unsigned NOT NULL auto_increment,
	`plot_id` int(10) unsigned NOT NULL,
	`caption` varchar(255) DEFAULT NULL,
	`thumbnail` varchar(200) NOT NULL,
	`full` varchar(200) NOT NULL,
	PRIMARY KEY  (`image_id`),
	KEY `plot_id` (`plot_id`),
	CONSTRAINT `fk_plot` FOREIGN KEY (`plot_id`) REFERENCES `plot` (`plot_id`) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


--
-- Definition of table `user_images`
--

CREATE TABLE  `user_images` (
	`image_id` int(10) unsigned NOT NULL auto_increment,
	`user_id` int(10) unsigned NOT NULL,
	`caption` varchar(255) DEFAULT NULL,
	`thumbnail` varchar(200) NOT NULL,
	`full` varchar(200) NOT NULL,
	PRIMARY KEY  (`image_id`),
	KEY `user_id` (`user_id`),
	CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");

$this->endSetup();
