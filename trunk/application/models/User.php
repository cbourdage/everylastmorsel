<?php


class Elm_Model_User extends Colony_Model_Abstract
{
    const EXCEPTION_EMAIL_NOT_CONFIRMED       = 1;
    const EXCEPTION_INVALID_EMAIL_OR_PASSWORD = 2;
    const EXCEPTION_EMAIL_EXISTS              = 3;

	private $_plots = array();

	private $_messages = array();

	/**
	 * Constructor
	 */
	public function _construct()
    {
        $this->_init('user');
    }

	/**
     * Processing object before save data
	 *
     * @return Colony_Model_Abstract
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
		$this->setAlias(substr($this->getEmail(), 0, strpos($this->getEmail(), '@')));
        return $this;
    }

	/**
     * Authenticate user
     *
     * @param  string $login
     * @param  string $password
     * @return true
     * @throws Exception
     */
    public function authenticate($login, $password)
    {
        $this->loadByEmail($login);
        if (!$this->validatePassword($password)) {
            throw new Colony_Exception('Invalid login or password', self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD);
        } else {
			$this->setLastLogin(new Zend_Db_Expr('now()'))->save();
		}

        return true;
    }

    /**
     * Load customer by email
     *
     * @param   string $email
     * @return  Elm_Model_User
     */
    public function loadByEmail($email)
    {
        $this->_getResource()->loadByEmail($this, $email);
        return $this;
    }

	/**
     * Load customer by email
     *
     * @param   string $alias
     * @return  Elm_Model_User
     */
    public function loadByAlias($alias)
    {
        $this->_getResource()->loadByAlias($this, $alias);
        return $this;
    }

	/**
	 * Checks the confirmation key exists
	 *
	 * @param $key
	 * @return bool
	 */
	public function checkConfirmationKey($key)
	{
		return $this->_getResource()->checkConfirmationKey($this, $key);
	}

	/**
	 * Checks if the current session user matches the
	 * instantiated user
	 *
	 * @param \Elm_Model_User $user
	 * @return bool
	 */
	public function isMe(Elm_Model_User $user)
	{
		$session = Elm::getSingleton('user/session');
		if ($session->isLoggedIn() && $session->user->getId() == $user->getId()) {
			return true;
		}
		return false;
	}

	/**
	 * Checks if current user is waiting approval
	 *
	 * @return boolean
	 */
	public function isWaitingApproval()
	{
		foreach ($this->getData('user_role') as $role) {
			if ($role['role'] != Elm_Model_Resource_Plot::ROLE_CREATOR && $role['role'] != Elm_Model_Resource_Plot::ROLE_OWNER) {
				return $role['is_approved'] == 0;
			}
		}
		return false;
	}

	/**
	 * Get plots
	 *
	 * @return mixed
	 */
	public function getPlots()
	{
		if (count($this->_plots) < 1 && count($this->getPlotIds()) > 0) {
			foreach ($this->getPlotIds() as $id => $role) {
				if ($role != Elm_Model_Resource_Plot::ROLE_CREATOR) {
					$plot = Elm::getModel('plot')->load($id);
					$plot->setUserRole($role);
					$this->_plots[] = $plot;
				}
			}
		}
		return $this->_plots;
	}

	public function hasPlots()
	{
		return count($this->getPlots()) > 0;
	}

    /**
     * Change customer password
     *
     * @param   string $newPassword
     * @return  this
     */
    public function changePassword($newPassword)
    {
        $this->_getResource()->changePassword($this, $newPassword);
        return $this;
    }

    /**
     * Get full customer name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }

    /**
     * Set plain and hashed password
     *
     * @param string $password
     * @return Elm_Model_User
     */
    public function setPassword($password)
    {
        $this->setData('password', $password);
        $this->setPasswordHash($this->hashPassword($password));
        return $this;
    }

    /**
     * Hash customer password
     *
     * @param   string $password
     * @param   string $salt
     * @return  string
     */
    public function hashPassword($password, $salt=null)
    {
        return Colony_Hash::getHash($password, !is_null($salt) ? $salt : 2);
    }

    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length=6)
    {
        return Colony_Hash::getRandomString($length);
    }

    /**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        if (!($hash = $this->getPasswordHash())) {
            return false;
        }
        return Colony_Hash::validateHash($password, $hash);
    }

	/**
	 * Returns a confirmation key for a user to authenticate against
	 *
	 * @return string
	 */
	protected function _getRandomConfirmationKey()
    {
        return md5(uniqid());
    }

    /**
     * Send email with new account specific information
	 *
     * @return Elm_Model_User
     */
    public function sendNewAccountEmail()
    {
		try {
			$this->setConfirmationKey($this->_getRandomConfirmationKey())->save();
			$EmailTemplate = new Elm_Model_Email_Template(array('template' => 'new-user.phtml'));
			$EmailTemplate->setParams(array(
				'user' => $this,
				'password' => $this->getPassword(),
				'confirmationKey' => $this->getConfirmationKey()
			));
			$EmailTemplate->send(array('email' => $this->getEmail(), 'name' => $this->getName()));
		} catch(Exception $e) {
			Elm::logException($e);
		}
        return $this;
    }

	/**
     * Send email after an account has been confirmed
	 *
     * @return Elm_Model_User
     */
    public function sendConfirmedAccountEmail()
    {
		try {
			$EmailTemplate = new Elm_Model_Email_Template(array('template' => 'user-confirmed.phtml'));
			$EmailTemplate->setParams(array('user' => $this));
			$EmailTemplate->send(array('email' => $this->getEmail(), 'name' => $this->getName()));
		} catch(Exception $e) {
			Elm::logException($e);
		}
        return $this;
    }

    /**
     * Send email with new temp/user password
	 * @TODO: create template, create action, test
     *
     * @return Elm_Model_User
     */
    public function sendPasswordResetEmail()
    {
        try {
			$EmailTemplate = new Elm_Model_Email_Template(array('template' => 'password-reset.phtml'));
			$EmailTemplate->setParams(array(
				'user' => $this,
				'password' => $this->getPassword(),
			));
			$EmailTemplate->send(array('email' => $this->getEmail(), 'name' => $this->getName()));
		} catch(Exception $e) {
			Elm::logException($e);
		}
        return $this;
        return $this;
    }

	/**
	 * Returns a user url
	 *
	 * @return string
	 */
	public function getUrl()
	{
		$helper = new Elm_View_Helper_Url();
		$url = $helper->url(null, array('alias' => $this->getAlias(), '_route' => 'user'));
		return $url;
	}

	/**
	 * Gets the role of a user on a plot
	 *
	 * @return string|null
	 */
	public function getRole()
	{
		foreach ($this->getUserRole() as $role) {
			if ($role['role'] != Elm_Model_Resource_Plot::ROLE_CREATOR && $role['role'] != Elm_Model_Resource_Plot::ROLE_WATCHER) {
				return $role['role'];
			}
		}
		return null;
	}


	public function getUnreadMessageCount()
	{
		if (!$this->_messages) {
			$this->_messages = Elm::getModel('communication')->getByUserId($this->getId());
		}
		return $this->_messages->count();
	}

    /**
     * Reset all model data
     *
     * @return Elm_Model_User
     */
    public function reset()
    {
        $this->setData(array());
        $this->setOrigData();
        return $this;
    }
}