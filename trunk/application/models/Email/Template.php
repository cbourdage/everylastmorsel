<?php

/**
 * TODO: Update template - create interface and add specific template sends (ie: user, plot, message, etc)
 * 		this should extend 'message' object (communication becomes message)
 */
class Elm_Model_Email_Template extends Colony_Object
{
	const DEFAULT_SUBJECT = 'Every Last Morsel Communication';
	const DEFAULT_FROM_EMAIL = 'comm@everylastmorsel.com';
	const DEFAULT_FROM_NAME = 'ELM Communication';

	/**
	 * @param $params
	 */
	public function __construct($params)
	{
		// Set data first
		$this->setData($params);

		// Set specifics
		$this->setSubject(self::DEFAULT_SUBJECT);
		$this->setFromEmail(self::DEFAULT_FROM_EMAIL);
		$this->setFromName(self::DEFAULT_FROM_NAME);
	}

	/**
	 * Sets the templates params
	 *
	 * @param $params
	 * @return Elm_Model_Email_Template
	 */
	public function setParams($params)
	{
		$this->setData('params', $params);
		return $this;
	}

	/**
	 * Sends email
	 *
	 * @param array $to
	 * @return Elm_Model_Email_Template
	 * @throws Colony_Exception
	 */
	public function send($to)
	{
		// create view object
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/views/emails/');

		// assign valeues
		foreach ($this->getParams() as $name => $value) {
			$html->assign($name, $value);
		}

		// create mail object
		$mail = new Zend_Mail('utf-8');

		if (!$this->getTemplate()) {
			throw new Colony_Exception('Invalid Email Template', 600);
		}

		// render view
		$bodyText = $html->render($this->getTemplate());

		// configure base stuff
		$mail->addTo($to['email'], $to['name']);
		$mail->setSubject($this->getSubject());
		$mail->setFrom($this->getFromEmail(), $this->getEmailName());
		$mail->setBodyHtml($bodyText);
		$mail->send();

		return $this;
	}
}