<?php
$this->startSetup();


$this->run("

ALTER TABLE `yields` ADD COLUMN `date_picked` DATETIME NOT NULL AFTER `plot_crop_id`;
ALTER TABLE `yields` ADD COLUMN `qty_for_sale` INT DEFAULT 0 AFTER `quantity_unit`;
ALTER TABLE `yields` ADD COLUMN `is_for_sale` TINYINT DEFAULT 0 AFTER `quantity_unit`;

");


$this->endSetup();
