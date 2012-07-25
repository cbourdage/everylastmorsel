<?php


class Elm_Model_Earlybirds extends Colony_Model_Abstract
{
	public function _construct()
    {
        $this->_init('earlybirds');
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
     * Send email with new account specific information
	 *
     * @return Elm_Model_User
     */
    public function sendEmailNotification()
    {
		try {
			$EmailTemplate = new Elm_Model_Email_Template(array('template' => 'coming-soon-notification.phtml'));
			$EmailTemplate->send(array('email' => $this->getEmail()));
		} catch(Exception $e) {
			Elm::logException($e);
		}
        return $this;
    }
}