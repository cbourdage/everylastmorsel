<?php

class Elm_Model_Yield_Purchasable extends Colony_Model_Abstract
{
	/**
	 * @var array
	 */
	private $_types = array();

	public function _construct()
    {
        $this->_init('yield_purchasable');
    }
}
