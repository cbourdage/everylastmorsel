<?php
$this->startSetup();

$this->run("
ALTER TABLE `user` ADD COLUMN `last_login` DATETIME DEFAULT '0000-00-00 00:00:00' AFTER `password_hash`;
ALTER TABLE `user` ADD COLUMN `active` TINYINT DEFAULT 1 AFTER `password_hash`;
ALTER TABLE `user` ADD COLUMN `is_new` TINYINT DEFAULT 1 AFTER `password_hash`;
ALTER TABLE `user` ADD COLUMN `visibility` VARCHAR(16) DEFAULT 'public' AFTER `password_hash`;


ALTER TABLE `plot` ADD COLUMN `active` TINYINT DEFAULT 1 AFTER `zipcode`;
ALTER TABLE `plot` ADD COLUMN `is_new` TINYINT DEFAULT 1 AFTER `zipcode`;


-- create images


-- create status messages


");

$this->endSetup();
