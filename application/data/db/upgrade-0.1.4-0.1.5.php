<?php
$this->startSetup();


$this->run("

ALTER TABLE `user` ADD COLUMN `website` VARCHAR(255) NULL AFTER `location`;

");

$this->endSetup();
