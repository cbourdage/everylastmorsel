<?php

class Elm_View_Helper_Form extends Zend_View_Helper_Abstract
{
	private $_session = null;

	private $_loginForm = null;

	private $_createForm = null;

	/**
	 * @return Elm_View_Helper_User
	 */
	public function Form()
	{
		$this->_session = Elm::getSingleton('user/session');
		return $this;
	}

	public function getLoginForm()
	{
		if (!$this->_loginForm) {
			$this->_loginForm = new Elm_Model_Form_User_Login();
		}
		return $this->_loginForm;
	}

    public function getCreateForm()
    {
        if (!$this->_createForm) {
            $this->_createForm = new Elm_Model_Form_User_Create();
        }
        return $this->_createForm;
    }
}