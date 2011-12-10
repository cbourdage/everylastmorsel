<?php

class Elm_Model_Session extends Zend_Session_Namespace
{
	public function __construct()
	{
		parent::__construct('elm', true);
	}

	/**
     * Retrieve Session Form Key
     *
     * @return string A 16 bit unique key for forms
     */
    public function getFormKey()
    {
        if (!$this->_form_key) {
            $this->_form_key = Colony_Hash::getRandomString(16);
        }
        return $this->_form_key;
    }
}