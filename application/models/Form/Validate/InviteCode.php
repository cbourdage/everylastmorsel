<?php

class Elm_Model_Form_Validate_InviteCode extends Zend_Validate_Abstract
{
    const NO_MATCH = 'noMatch';

	private $_model;

    protected $_messageTemplates = array(
        self::NO_MATCH => 'Oops! "%value%" is not a valid invitation code',
    );

	/**
	 * Checks if current invite code exists in system
	 *
	 * @param string $value
	 * @param null $context
	 * @return bool
	 */
    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
		$code = Elm::getModel('inviteCode')->load($value);
        if ($code->getCode()) {
            return true;
        }

        $this->_error(self::NO_MATCH);
        return false;
    }
}
