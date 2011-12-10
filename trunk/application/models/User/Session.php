<?php

/**
 * User session model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Elm_Model_User_Session extends Zend_Session_Namespace
{
    /**
     * Customer object
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_user;

	public function __construct()
	{
		parent::__construct('user');
	}

    /**
     * Set customer object and setting customer id in session
     *
     * @param   Elm_Model_User $user
     * @return  Elm_Model_User_Session
     */
    public function setUser(Elm_Model_User $user)
    {
        $this->_user = $user;
        $this->id = $user->getId();
        return $this;
    }

    /**
     * Retrieve costomer model object
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getUser()
    {
        if ($this->_user instanceof Elm_Model_User) {
            return $this->_user;
        }

        $user = Bootstrap::getModel('user');
        if ($this->id) {
            $user->load($this->id);
        }

        $this->setUser($user);
        return $this->_user;
    }

    /**
     * Retrieve customer id from current session
     *
     * @return int || null
     */
    public function getUserId()
    {
        if ($this->isLoggedIn()) {
            return $this->id;
        }
        return null;
    }

    /**
     * Checking custommer loggin status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool)$this->id && (bool)$this->checkUserId($this->id);
    }

	// @TODO database lookkup
	public function checkUserId($id)
	{
		return true;
	}

    /**
     * Customer authorization
     *
     * @param   string $username
     * @param   string $password
     * @return  bool
     */
    public function login($username, $password)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $user = Bootstrap::getModel('user');
        if ($user->authenticate($username, $password)) {
            $this->setUser($user);
            //$this->renewSession();
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
            $this->beforeAuthUrl = $action->getFrontController()->getCurrentUrl();
            $action->getResponse()->setRedirect('/user/login');
            return false;
        }
        return true;
    }
}
