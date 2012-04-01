<?php
$this->startSetup();

$this->run("

ALTER TABLE `user` ADD COLUMN `about` TEXT DEFAULT NULL AFTER `location`;
ALTER TABLE `plot` ADD COLUMN `about` TEXT DEFAULT NULL AFTER `zipcode`;

ALTER TABLE `user` CHANGE COLUMN `active` `is_active` TINYINT DEFAULT 1;
ALTER TABLE `plot` CHANGE COLUMN `active` `is_active` TINYINT DEFAULT 1;

");

$this->endSetup();
