<?php
$this->startSetup();


$this->run("

ALTER TABLE `plot_status_updates` MODIFY COLUMN `type` ENUM('text', 'image', 'link', 'crop', 'yield') DEFAULT 'text';

ALTER TABLE `yields` ADD COLUMN `quantity_unit` VARCHAR(32) AFTER `quantity`;
ALTER TABLE `yields_purchasable` ADD COLUMN `quantity_unit` VARCHAR(32) AFTER `quantity`;

");


$this->endSetup();
