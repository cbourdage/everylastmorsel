<?php
$this->startSetup();

$this->run("

DROP TABLE IF EXISTS `plot_images`;
DROP TABLE IF EXISTS `user_images`;

--
-- Definition of table `plot_images`
--

DROP TABLE IF EXISTS `plot_images`;
CREATE TABLE  `plot_images` (
  `image_id` int(10) unsigned NOT NULL auto_increment,
  `plot_id` int(10) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `size` varchar(32) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `full` varchar(255) NOT NULL,
  `exif_data` TEXT DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `size` varchar(32) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `full` varchar(255) NOT NULL,
  `exif_data` TEXT DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`image_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");

$this->endSetup();
