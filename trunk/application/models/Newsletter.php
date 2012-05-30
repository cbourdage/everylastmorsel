<?php


class Elm_Model_Newsletter extends Colony_Model_Abstract
{
	public function _construct()
    {
        $this->_init('newsletter');
    }

	/**
	 * Checks if email exists.
	 *
	 * @param $email
	 * @return boolean
	 */
	public function isTaken($email)
	{
		return $this->getResource()->emailLookup($email);
	}

	/**
	 * Signs up a user
	 *
	 * @param $email
	 * @return bool
	 */
	public function signUp($email)
	{
		if ($this->isTaken($email)) {
			return true;
		}

		try {
			$this->setEmail($email)->save();
			return true;
		} catch (Exception $e) {
			Elm::logException($e);
			return false;
		}
	}
}