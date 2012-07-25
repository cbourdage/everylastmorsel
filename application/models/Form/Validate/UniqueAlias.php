<?php

class Elm_Model_Form_Validate_UniqueAlias extends Zend_Validate_Abstract
{
    const ALIAS_EXISTS = 'aliasExists';

	private $_model;

    protected $_messageTemplates = array(
        self::ALIAS_EXISTS => 'Username "%value%" already exists.',
    );

	/**
	 * Checks if current alias exists in system
	 *
	 * @param string $value
	 * @param null $context
	 * @return bool
	 */
    public function isValid($value, $context = null)
    {
		// Validate current users alias as true
		if (Elm::getSingleton('user/session')->user->getAlias() == $value) {
			return true;
		}

        $this->_setValue($value);
		$user = Elm::getModel('user')->loadByAlias($value);
        if (!$user->getAlias()) {
            return true;
        }

        $this->_error(self::ALIAS_EXISTS);
        return false;
    }
}
