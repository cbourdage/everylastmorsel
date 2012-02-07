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
				'message' => $this->getMessage()
			));
			$EmailTemplate->send(array('email' => 'collin.bourdage@gmail.com', 'name' => 'Collin Bourdage'));

			// Save message
			$this->setDelivered(true);
			$this->save();
			return true;
		} catch(Exception $e) {
			Bootstrap::logException($e);
			return false;
		}
	}
}