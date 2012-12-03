<?php
$this->startSetup();


$this->run("

ALTER TABLE `plot_status_updates` MODIFY COLUMN `type` ENUM('text', 'image', 'link', 'crop', 'yield') 'text';

");


$this->endSetup();
