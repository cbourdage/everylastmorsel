<?php

/**
 * TODO break down into a 'communication' object that holds 'message' objects
 */
class Elm_Model_Communication extends Colony_Model_Abstract
{
	public function _construct()
    {
        $this->_init('communication');
    }

	/**
	 * Sets the appropriate data
	 *
	 * @param array $params
	 * @return Elm_Model_Communication
	 */
	public function init($params)
	{
		$this->setData($params);
		$this->setToUser(Elm::getModel('user')->load($this->getUserToId()));
		$this->setFromUser(Elm::getModel('user')->load($this->getUserFromId()));
		return $this;
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function getByUserId($id)
	{
		return $this->_getResource()->getByUserId($id);
	}

	/**
	 * Sends the email
	 *
	 * @return bool
	 */
	public function send()
	{
		try {
			$EmailTemplate = new Elm_Model_Email_Template(array('template' => 'communication.phtml'));
			$EmailTemplate->setParams(array(
				'reason' => $this->getSubject(),
				'fromName' => $this->getName(),
				'fromEmail' => $this->getEmail(),
				'message' => $this->getMessage(),
				'fromUser' => $this->getFromUser(),
				'toUser' => $this->getFromUser()
			));
			$EmailTemplate->send(array('email' => $this->getToUser()->getEmail(), 'name' => $this->getToUser()->getName()));

			// Save message
			$this->setDelivered(true)->save();
			return true;
		} catch(Exception $e) {
			$this->setDelivered(false)->save();
			Elm::logException($e);
			return false;
		}
	}
}