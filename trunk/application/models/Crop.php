<?php

class Elm_Model_Crop extends Colony_Model_Abstract
{
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
}
