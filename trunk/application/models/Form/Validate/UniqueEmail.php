<?php

class Elm_Model_Form_Validate_UniqueEmail extends Zend_Validate_Abstract
{
    const EMAIL_EXISTS = 'emailExists';

	private $_model;

    protected $_messageTemplates = array(
        self::EMAIL_EXISTS => 'Email "%value%" already exists in our system',
    );

    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
		$user = Bootstrap::getModel('user')->loadByEmail($value);
        if (!$user->getEmail()) {
            return true;
        }

        $this->_error(self::EMAIL_EXISTS);
        return false;
    }
}
