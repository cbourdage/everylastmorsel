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

	public function isValidReply($data)
	{
		if (!strlen($data['parent_id'])) {
			return false;
		}

		if (!strlen($data['to_user_id'])) {
			return false;
		}

		if (!strlen($data['from_user_id'])) {
			return false;
		}

		if (!strlen($data['subject'])) {
			return false;
		}

		if (!strlen($data['subject'])) {
			return false;
		}

		return true;
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
	 * @param $userId
	 * @return mixed
	 */
	public function getByUserId($userId)
	{
		return $this->_getResource()->getAll($userId);
	}

	/**
	 * Retrieves messages based on any specified/set filters
	 *
	 * @return mixed
	 */
	public function retrieve()
	{
		if (!$this->getUserId()) {
			$this->setUserId(Elm::getSingleton('user/session')->getUserId());
		}

		if ($this->getFilterBy()) {
			return $this->_getResource()->getFiltered($this);
		}

		return $this->_getResource()->getAll($this->getUserId());
	}

	/**
	 * Archives current message
	 *
	 * @return Elm_Model_Communication
	 */
	public function archive()
	{
		$this->setIsArchived(true)->save();
		return $this;
	}

	/**
	 * Sends reply
	 *
	 * @return bool
	 */
	public function reply()
	{
		$this->setIsRead(true);
		return $this->send();
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
			$EmailTemplate->setFromName($this->getName());
			//$EmailTemplate->setFromEmail($this->getEmail());
			//Elm::log($EmailTemplate);
			//die('dead sending');
			//$EmailTemplate->send(array('email' => $this->getToUser()->getEmail(), 'name' => $this->getToUser()->getName()));

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