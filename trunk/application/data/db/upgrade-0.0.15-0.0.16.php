<?php
$this->startSetup();

$this->run("

ALTER TABLE `plot` ADD COLUMN `address` VARCHAR(64) NULL AFTER `longitude`;
ALTER TABLE `plot` ADD COLUMN `city` VARCHAR(64) NULL AFTER `address`;
ALTER TABLE `plot` ADD COLUMN `state` VARCHAR(32) NULL AFTER `city`;
ALTER TABLE `plot` ADD COLUMN `privacy` VARCHAR(16) NULL AFTER `image_retrieved_at`;

");

$this->endSetup();
