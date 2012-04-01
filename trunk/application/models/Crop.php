<?php

class Elm_Model_Crop extends Colony_Model_Abstract
{
	public function _construct()
    {
        $this->_init('crop');
    }

	/**
	 * Validates data on plot crop
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return true;
	}
}
