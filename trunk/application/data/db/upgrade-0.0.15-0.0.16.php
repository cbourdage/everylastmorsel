<?php
$this->startSetup();

$this->run("

ALTER TABLE `user` ADD COLUMN `address` VARCHAR(64) NULL AFTER `location`;
ALTER TABLE `user` ADD COLUMN `city` VARCHAR(64) NULL AFTER `address`;
ALTER TABLE `user` ADD COLUMN `state` VARCHAR(32) NULL AFTER `city`;
ALTER TABLE `user` ADD COLUMN `zipcode` VARCHAR(32) NULL AFTER `state`;


ALTER TABLE `plot` ADD COLUMN `address` VARCHAR(64) NULL AFTER `longitude`;
ALTER TABLE `plot` ADD COLUMN `city` VARCHAR(64) NULL AFTER `address`;
ALTER TABLE `plot` ADD COLUMN `state` VARCHAR(32) NULL AFTER `city`;
ALTER TABLE `plot` ADD COLUMN `privacy` VARCHAR(16) NULL AFTER `image_retrieved_at`;

");

$this->endSetup();
