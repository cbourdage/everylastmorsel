<?php
$this->startSetup();

$this->run("
DROP TABLE IF EXISTS `user_plot_relationships`;
CREATE TABLE `user_plot_relationships` (
  `user_id` int(10) unsigned NOT NULL,
  `plot_id` int(10) unsigned NOT NULL,
  `role` varchar(24) DEFAULT NULL,
  PRIMARY KEY (`plot_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds all links between users and plots';
");

$this->endSetup();
