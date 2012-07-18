<?php
$this->startSetup();

$this->run("

TRUNCATE crops;
ALTER TABLE `crops` CHANGE COLUMN `breeder` `breeder` varchar(64) DEFAULT NULL AFTER `variety`;
ALTER TABLE `crops` CHANGE COLUMN `similar` `similar` varchar(64) DEFAULT NULL AFTER `characteristics`;
ALTER TABLE `crops` CHANGE COLUMN `characteristics` `characteristics` text DEFAULT NULL AFTER `breeder`;
ALTER TABLE `crops` CHANGE COLUMN `adaptation` `adaptation` varchar(255) DEFAULT NULL AFTER `similar`;
ALTER TABLE `crops` CHANGE COLUMN `resistance` `resistance` varchar(255) DEFAULT NULL AFTER `adaptation`;
ALTER TABLE `crops` CHANGE COLUMN `parentage` `parentage` varchar(255) DEFAULT NULL AFTER `resistance`;

DROP INDEX `crop_name_idx` ON `crops`;
DROP INDEX `crop_variety_idx` ON `crops`;
DROP INDEX `crop_type_idx` ON `crops`;

CREATE INDEX `crop_name_idx` ON `crops` (name(6));
CREATE INDEX `crop_variety_idx` ON `crops` (variety(6));
CREATE INDEX `crop_type_idx` ON `crops` (type(4));

");

$directory = APPLICATION_PATH . '/data/vegetables/csv';
if ($handle = opendir($directory)) {
	$ctr = 0;
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && is_file($directory . DIRECTORY_SEPARATOR . $file)) {
            $type = str_replace('_', ' ', str_replace('_out.csv', '', $file));
			Elm::getModel('crop')->import($file, $type);
			$ctr++;
        }
    }
    closedir($handle);
}
Elm::log(sprintf('imported: %s', $ctr), Zend_Log::DEBUG, 'import/import.log');


$this->endSetup();
