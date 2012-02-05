<?php


class Elm_Model_Communication extends Colony_Model_Abstract
{
	public function _construct()
    {
        $this->_init('communication');
    }

	public function init($params)
	{
		$this->setData($params);
		return $this;
	}

	public function send()
	{
		try {
			$EmailTemplate = new Elm_Model_Email_Template(array('template' => 'communication.phtml'));
			$EmailTemplate->setParams(array(
					'reason' => $this->getSubject(),
					'fromName' => $this->getName(),
					'fromEmail' => $this->getEmail(),
					'message' => $this->getMessage()
				)
			);
			$EmailTemplate->send(array('email', 'name'));

			// Save message
			$this->save();
		} catch(Exception $e) {
			Bootstrap::logException($e);
		}
		return $this;
	}
}