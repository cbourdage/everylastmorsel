<?php
$this->startSetup();

$this->run("

ALTER TABLE `plot` ADD COLUMN `image_retrieved_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `about`;
ALTER TABLE `plot` ADD COLUMN `image` varchar(255) DEFAULT NULL AFTER `about`;

CREATE TABLE `invite_codes` (
  `code` VARCHAR(16) NOT NULL,
  `total_available` int(10) unsigned NOT NULL,
  `total_used` int(10) unsigned DEFAULT 0,
  `is_active` tinyint DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds all invitiation codes';

INSERT INTO `invite_codes` VALUES ('GROW4YOU', 50, 0, 1, NOW());

");

$this->endSetup();
