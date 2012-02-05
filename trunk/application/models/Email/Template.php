<?php

class Elm_Model_Email_Template extends Colony_Object
{
	const DEFAULT_SUBJECT = 'Every Last Morsel Communication';

	/**
	 * @param $params
	 */
	public function __construct($params)
	{
		$this->setData($params);
		$this->setSubject(self::DEFAULT_SUBJECT);
		$this->setFromEmail('comm@everylastmorsel.com');
		$this->setFromName('Every Last Morsel Communication');
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
	 * @param $to
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
		$mail->addTo($to);
		$mail->setSubject($this->getSubject());
		$mail->setFrom($this->getFromEmail(), $this->getEmailName());
		$mail->setBodyHtml($bodyText);
		$mail->send();

		Bootstrap::log($mail->getBodyHtml());

		return $this;
	}
}