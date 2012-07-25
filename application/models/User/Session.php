<?php

/**
 * User session model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Elm_Model_User_Session extends Colony_Session
{
	/**
	 * Constructor to initialize namespace
	 */
	public function __construct()
	{
		parent::__construct('user');
	}

	/**
	 * Sets the status of a user as logged in
	 *  - initializes session for user
	 *
	 * @param $user
	 * @return Elm_Model_User_Session
	 */
	public function setUserAsLoggedIn($user)
	{
		$this->setUser($user);
		return $this;
	}

    /**
     * Set customer object and setting customer id in session
     *
     * @param   Elm_Model_User $user
     * @return  Elm_Model_User_Session
     */
    public function setUser(Elm_Model_User $user)
    {
        $this->user = $user;
        $this->id = $user->getUserId();
        return $this;
    }

    /**
     * Retrieve user model object
     *
     * @return Elm_Model_User
     */
    public function getUser()
    {
        if ($this->user instanceof Elm_Model_User) {
			$this->user = Elm::getModel('user')->load($this->getUserId());
            return $this->user;
        }
        $this->user = Elm::getModel('user');
        return $this->user;
    }

    /**
     * Retrieve customer id from current session
     *
     * @return int || null
     */
    public function getUserId()
    {
        if ($this->isLoggedIn()) {
            return $this->user->getUserId();
        }
        return null;
    }

    /**
     * Checking user login status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool)$this->user && (bool)$this->checkUserId($this->user->getId());
    }

	/**
	 * Checks the id is valid
	 *
	 * @param $id
	 * @return bool
	 */
	public function checkUserId($id)
	{
		return Elm::getModel('user')->getResource()->checkUserId($id);
	}

    /**
     * Auth user - if false throw Colony_Exception
     *
     * @param   string $username
     * @param   string $password
     * @return  bool
     */
    public function login($username, $password)
    {
        $user = Elm::getModel('user');
        if ($user->authenticate($username, $password)) {
            $this->setUserAsLoggedIn($user);
            return true;
        }
        return false;
    }

    /**
     * Logout customer
     *
     * @return Mage_Customer_Model_Session
     */
    public function logout()
    {
        if ($this->isLoggedIn()) {
            $this->id = null;
            $this->unsetAll();
        }
        return $this;
    }

    /**
     * Authenticate controller action by login customer
     *
     * @param   Zend_Controller_Action $action
     * @return  bool
     */
    public function authenticate(Zend_Controller_Action $action)
    {
        if (!$this->isLoggedIn()) {
            $this->beforeAuthUrl = $action->getCurrentUrl();
			Elm::log('not logged in: ' . __METHOD__);
			Elm::log($this->beforeAuthUrl);
            return false;
        }
        return true;
    }
}
