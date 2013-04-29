<?php

class Colony_Session extends Zend_Session_Namespace
{
	const DEFAULT_LOG_FILE = 'app.log';
	const EXCEPTION_FILE = 'exception.log';

	// @TODO Should create a message object that contains specific types of messages?
	const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const SUCCESS   = 'success';

	/**
     * Retrieve Session Form Key
     *
     * @return string A 16 bit unique key for forms
     */
    public function getFormKey()
    {
        if (!$this->_form_key) {
            $this->_form_key = Colony_Hash::getRandomString(16);
        }
        return $this->_form_key;
    }

	/**
     * Retrieve messages from session
     *
	 * @param 	bool $clear
     * @return  Array
     */
    public function getMessages($clear = false)
    {
		$messages = $this->_messages;
		if ($clear === true) {
			$this->_messages = array();
		}
        return $messages;
    }

    /**
     * Not Mage exeption handling
     *
     * @param   Exception $exception
     * @param   string $alternativeText
     * @return  Colony_Session
     */
    public function addException(Exception $exception, $alternativeText)
    {
        // log exception to exceptions log
        $message = sprintf('Exception message: %s%sTrace: %s', $exception->getMessage(), "\n", $exception->getTraceAsString());
        Elm::log($message, Zend_Log::DEBUG, self::EXCEPTION_FILE);
        $this->addMessage(self::ERROR, $alternativeText);
        return $this;
    }

    /**
     * Adding new message to message collection
     *
	 * @param   string $type
     * @param   string $message
     * @return  Colony_Session
     */
    public function addMessage($type, $message)
    {
		if (!isset($this->_messages[$type])) {
			$this->_messages[$type] = array();
		}
        $this->_messages[$type][] = $message;
        return $this;
    }

    /**
     * Adding new error message
     *
     * @param   string $message
     * @return  Colony_Session
     */
    public function addError($message)
    {
        $this->addMessage(self::ERROR, $message);
        return $this;
    }

    /**
     * Adding new warning message
     *
     * @param   string $message
     * @return  Colony_Session
     */
    public function addWarning($message)
    {
        $this->addMessage(self::WARNING, $message);
        return $this;
    }

    /**
     * Adding new nitice message
     *
     * @param   string $message
     * @return  Colony_Session
     */
    public function addNotice($message)
    {
        $this->addMessage(self::NOTICE, $message);
        return $this;
    }

    /**
     * Adding new success message
     *
     * @param   string $message
     * @return  Colony_Session
     */
    public function addSuccess($message)
    {
        $this->addMessage(self::SUCCESS, $message);
        return $this;
    }

    /**
     * Adding messages array to message collection
     *
     * @param   array $messages
     * @return  Colony_Session
     */
    public function addMessages($messages)
    {
        if (is_array($messages)) {
            foreach ($messages as $type => $message) {
                $this->addMessage($type, $message);
            }
        }
        return $this;
    }
}