<?php
$this->startSetup();

$this->run("

ALTER TABLE `user_plot_relationships` ADD COLUMN `is_approved` tinyint DEFAULT 0 AFTER `role`;
ALTER TABLE `plot_images` ADD COLUMN `date_taken` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `exif_data`;
ALTER TABLE `plot_images` ADD COLUMN `modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_at`;

");

$this->endSetup();
