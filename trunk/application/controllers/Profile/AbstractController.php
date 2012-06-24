<?php

require_once 'controllers/AbstractController.php';

class Elm_Profile_AbstractController extends Elm_AbstractController
{
	protected $_user = null;

	/**
	 *
	 */
	public function preDispatch()
	{
		parent::preDispatch();

		$action = $this->getRequest()->getActionName();
        $pattern = '/^(create|login|forgot)/i';
		if (!preg_match($pattern, $action)) {
			if (!$this->_getSession()->authenticate($this)) {
				$this->_redirect('/profile/login');
			}
		}
	}

	/**
	 * @return Elm_Profile_AbstractController
	 */
	protected function _init()
	{
		$user = $this->_getSession()->getUser();
		$this->view->headTitle()->prepend($user->getFirstname() . ' ' . $user->getLastname());

		$this->_user = $user;
		$this->view->user = $user;
		Zend_Registry::set('current_user', $user);

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
         	$layout = $this->_helper->layout();
			$layout->setLayout('profile-layout');
        }

		$this->view->placeholder('contact-modal')->set($this->view->render('communication/_modal.phtml'));
		$this->view->placeholder('sidebar')->set($this->view->render('profile/_sidebar.phtml'));
	}
}