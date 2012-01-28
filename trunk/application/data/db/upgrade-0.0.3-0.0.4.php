<?php
$this->startSetup();

$this->run("
ALTER TABLE `user` ADD COLUMN 'last_login' datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER COLUMN 'password_hash';
ALTER TABLE `user` ADD COLUMN 'active' TINYINT DEFAULT 1 AFTER COLUMN 'password_hash';
ALTER TABLE `user` ADD COLUMN 'is_new' TINYINT DEFAULT 1 AFTER COLUMN 'password_hash';
ALTER TABLE `user` ADD COLUMN 'visibility' VARCHAR(16) DEFAULT 'public' AFTER COLUMN 'password_hash';


ALTER TABLE `plot` ADD COLUMN 'active' TINYINT DEFAULT 1 AFTER COLUMN 'zipcode';
ALTER TABLE `plot` ADD COLUMN 'is_new' TINYINT DEFAULT 1 AFTER COLUMN 'zipcode';


-- create images


-- create status messages


");

$this->endSetup();
