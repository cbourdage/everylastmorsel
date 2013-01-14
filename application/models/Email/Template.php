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
		if (count($this->getParams())) {
			foreach ($this->getParams() as $name => $value) {
				$html->assign($name, $value);
			}
		}

		// create mail object
		$mail = new Zend_Mail('utf-8');

		if (!$this->getTemplate()) {
			throw new Colony_Exception('Invalid Email Template', 600);
		}

		// render view
		$bodyText = $html->render($this->getTemplate());

		// set headers
		$mail->addHeader('Organization', 'Every Last Morsel');

		// configure base stuff
		$mail->addTo($to['email'], $to['name']);
		$mail->setSubject($this->getSubject());
		$mail->setFrom($this->getFromEmail(), $this->getFromName());
		$mail->setBodyHtml($bodyText);

		//Elm::log($mail, Zend_Log::INFO, 'communication-emails.log');
		//Elm::log($mail->getRecipients(), Zend_Log::INFO, 'communication-emails.log');
		$mail->send();

		return $this;
	}
}