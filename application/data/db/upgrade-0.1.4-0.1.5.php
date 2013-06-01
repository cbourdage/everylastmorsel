<?php
$this->startSetup();


$this->run("

ALTER TABLE `plot` AUTO_INCREMENT = 100;
ALTER TABLE `user` AUTO_INCREMENT = 100;
ALTER TABLE `user` ADD COLUMN `website` VARCHAR(255) NULL AFTER `location`;

");

$this->endSetup();
