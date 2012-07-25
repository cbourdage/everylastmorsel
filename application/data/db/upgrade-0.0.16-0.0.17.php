<?php
$this->startSetup();

$this->run("

DROP TABLE IF EXISTS `crops_inventory_items`;
CREATE TABLE  `crops_inventory_items` (
	`item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`crop_id` int(10) unsigned NOT NULL,
	`quantity` decimal(12,4) DEFAULT 0.0000,
	`quantity_increments` decimal(12,4) DEFAULT 0.0000,
	`max_quantity_increments` decimal(12,4) DEFAULT 0.0000,
	`min_quantity_increments` decimal(12,4) DEFAULT 0.0000,
	`quantity_decrements` decimal(12,4) DEFAULT 0.0000,
	`max_quantity_decrements` decimal(12,4) DEFAULT 0.0000,
	`min_quantity_decrements` decimal(12,4) DEFAULT 0.0000,
	`low_stock_quantity` decimal(12,4) DEFAULT 0.0000,
	`is_in_stock` tinyint unsigned DEFAULT 1,
	`is_active` tinyint DEFAULT 1,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY  (`item_id`),
	CONSTRAINT `fk_crops_inventory_item_id` FOREIGN KEY (`crop_id`) REFERENCES `plot_crops` (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `invite_codes`;
CREATE TABLE `invite_codes` (
  `code` VARCHAR(16) NOT NULL,
  `description` text NOT NULL DEFAULT '',
  `total_available` int(10) unsigned NOT NULL,
  `total_used` int(10) unsigned DEFAULT 0,
  `is_active` tinyint DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds all invitiation codes';

INSERT INTO `invite_codes` (code, description, total_available, total_used, is_active, created_at)
VALUES ('GROW4YOU', 'General bucket for testing.', 50, 0, 1, NOW()),
	('PERSONALINVITE', 'I have about a dozen farms & gardens in mind with as many as 4-5 potential users associated with each.', 100, 0, 1, NOW()),
	('COMMPLOT', 'The Peterson Garden Project & Root Riot will serve as a good test of our ability to manage community gardens.', 100, 0, 1, NOW()),
	('EARLYBIRD', 'To be shared with those who have signed up @ everylastmorsel.com.', 200, 0, 1, NOW()),
	('ADDURBANAG', 'Registration code to be sent out through the AUA listserv.', 250, 0, 1, NOW()),
	('PEASINAPOD', 'Invite code for early adopters to share with friends.', 300, 0, 1, NOW());


ALTER TABLE `plot` ADD COLUMN `visibility` varchar(16) DEFAULT 'public' AFTER `privacy`;
ALTER TABLE `user_plot_relationships` ADD COLUMN `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `is_approved`;

");

$this->endSetup();
