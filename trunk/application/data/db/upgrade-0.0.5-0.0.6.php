<?php
$this->startSetup();

$this->run("

ALTER TABLE `plot` AUTO_INCREMENT = 1000;


-- create images
CREATE TABLE `images` (
  `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,

  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Application configuration settings';

-- create status messages


");

$this->endSetup();
