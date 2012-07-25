<?php

class Elm_Model_Crop extends Colony_Model_Abstract
{
	/**
	 * @var array
	 */
	private $_types = array();

	public function _construct()
    {
        $this->_init('crop');
    }

	/**
	 * @param $name
	 * @return bool|Elm_Model_Crop
	 */
	public function lookupLoad($name)
	{
		$this->_getResource()->loadByName($this, $name);
		return $this->getId() ? $this : false;
	}

	/**
	 * @return mixed
	 */
	public function getCrops()
	{
		return $this->_getResource()->getCrops();
	}

	/**
	 * @return array
	 */
	public function getCropTypes()
	{
		$this->_types = $this->_getResource()->uniqueTypes();
		return $this->_types;
	}

	/**
	 * Searches for corresponding varieties based on type and search term
	 *
	 * @param $type
	 * @param $term
	 * @param null $limit
	 * @return array
	 */
	public function getVarieties($type, $term=null, $limit=null)
	{
		$results = array();
		$varieties = $this->_getResource()->searchVarieties($term, $type, $limit);
		foreach ($varieties as $cropId => $variety) {
			$results[] = array('id' => $cropId, 'value' => $variety, 'label' => $variety);
		}
		return $results;
	}

	/**
	 * Validates data on plot crop
	 *
	 * @return bool
	 */
	public function isValid()
	{
		if ($this->getId()) {
			return true;
		}

		if (!$this->getName()) {
			return false;
		}

		if (!$this->getType()) {
			return false;
		}

		return true;
	}

	/**
	 * Imports files provided into database
	 *
	 * @param $file
	 * @param $type
	 */
	public function import($file, $type)
	{
		$logFile = 'import/crops_' . str_replace(' ', '_', $type) . '.log';
		$file = Elm::getBaseDir('application/data/vegetables/csv/') . $file;
		if ($fh = fopen($file, 'r')) {
			$data = fgetcsv($fh, 10000, "|");
			//Elm::log($data, Zend_Log::DEBUG, $logFile);
			try {
				while (($data = fgetcsv($fh, 1000, "|")) !== FALSE) {
					/*$results = $this->getResource()->searchVarieties($data[0], $type, 1);
					if (count($results)) {
						Elm::log(sprintf('exists: %s, %s', $data[0], $type), Zend_Log::DEBUG, 'import/import.log');
						continue;
					}*/

					$crop = new Elm_Model_Crop();
					$crop->setType($type);
					$crop->setName($data[0]);
					$crop->setVariety($data[0]);
					$crop->setBreeder($data[1]);
					$crop->setCharacteristics($data[3]);
					$crop->setSimilar($data[4]);
					$crop->setAdaptation($data[4]);
					$crop->setResistance($data[4]);
					$crop->setParentage($data[4]);
					Elm::log($crop, Zend_Log::DEBUG, $logFile);
					$crop->save();
				}
			} catch(Colony_Exception $e) {
				Elm::throwException($e);
			}
			fclose($fh);
		} else {
			Elm::throwException('failed to open: ' . $file);
		}
	}
/**
 * 2012-07-08T23:29:57+00:00 DEBUG (7): Array
   	(
    	[0] => Name
    	[1] => Breeder
    	[2] => Vendor
    	[3] => Characteristics
    	[4] => Similar
    	[5] => Adaptation
    	[6] => Resistance
    	[7] => Parentage
 	)
 */

}
