<?php

require_once 'controllers/AbstractController.php';

class Elm_User_AbstractController extends Elm_AbstractController
{
	protected $_session;

	/**
	 * @var Elm_Model_User
	 */
	protected $_user;

	protected function _getSession()
	{
		if (!$this->_session) {
			$this->_session = Elm::getSingleton('user/session');
		}
		return $this->_session;
	}

	protected function _init()
	{
		//$this->_user = Elm::getModel('user')->loadByAlias($this->getRequest()->getParam('u'));
		$this->_user = Elm::getModel('user')->load($this->getRequest()->getParam('u'));
		Zend_Registry::set('current_user', $this->_user);

		$this->view->user = $this->_user;
		$this->view->canContact = ($this->_user->getVisibility() == Elm_Model_Form_User_Settings::VISIBILITY_PUBLIC);
		$this->view->headTitle()->prepend($this->_user->getName());

		return $this;
	}

	/**
	 * Initializes the User layout objects
	 */
	protected function _initLayout()
	{
		$action = $this->getRequest()->getActionName();
        $pattern = '/^(create|login)/i';
        if (!preg_match($pattern, $action)) {
         	$layout = $this->getHelper()->layout();
			$layout->setLayout('profile-layout');
        }

		$this->view->placeholder('contact-modal')->set($this->view->render('communication/contact/user.phtml'));
		$this->view->placeholder('sidebar')->set($this->view->render('user/_sidebar.phtml'));
	}
}