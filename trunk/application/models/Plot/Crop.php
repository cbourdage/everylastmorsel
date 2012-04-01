<?php

class Elm_Model_Plot_Crop extends Colony_Model_Abstract
{
	private $_crop;

	public function _construct()
    {
        $this->_init('plot_crop');
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
