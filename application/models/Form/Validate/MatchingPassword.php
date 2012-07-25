<?php


class Elm_Model_Form_Validate_MatchingPassword extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Incorrect password'
    );

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);
		$user = Elm::getSingleton('user/session')->user;
        if ($user->validatePassword($value)) {
            return true;
        }

        $this->_error(self::NOT_MATCH);
		return false;
    }
}
