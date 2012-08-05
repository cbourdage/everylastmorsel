<?php
$this->startSetup();

$this->run("
");

$directory = APPLICATION_PATH . '/data/vegetables/csv';
if ($handle = opendir($directory)) {
	$ctr = 0;
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && is_file($directory . DIRECTORY_SEPARATOR . $file)) {
            $type = str_replace('_', ' ', str_replace('_out.csv', '', $file));
			$variety = ucwords($type);
			$logFile = 'import/default_crops.log';
			try {
				// add data and save
				$crop = new Elm_Model_Crop();
				$crop->setType($type);
				$crop->setName($variety);
				$crop->setVariety($variety);
				Elm::log($crop, Zend_Log::DEBUG, $logFile);
				//die('dead to me');
				$crop->save();
			} catch(Colony_Exception $e) {
				Elm::throwException($e);
			}
			$ctr++;
        }
    }
    closedir($handle);
}
Elm::log(sprintf('imported: %s', $ctr), Zend_Log::DEBUG, 'import/import.log');

$this->endSetup();
