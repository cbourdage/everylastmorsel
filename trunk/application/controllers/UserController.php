<?php

class Elm_UserController extends Colony_Controller_Action
{
	protected function _getSession()
	{
		return Bootstrap::getSingleton('user/session');
	}
	public function preDispatch()
	{
        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        $pattern = '/^(create|login|forgotpassword)/i';
        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->_redirect('/');
            }
        }
	}

	public function postDispatch()
	{
		parent::postDispatch();
	}

	public function indexAction()
	{
		$this->_forward('view');
	}

	public function viewAction()
	{
		$this->view->message = 'You are logged in!';
	}

	// @TODO create error messages
	public function createAction()
	{
		$session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('/u/' . $session->getUser()->alias);
            return;
        }

		$form = new Elm_Model_User_Form_Create();
		if ($this->getRequest()->isPost()) {
			$errors = array();
			$post = $this->getRequest()->getPost();
			$user = Bootstrap::getModel('user');
			if ($form->isValid($post)) {
				$user->setData($post)
					->setPassword($this->getRequest()->getPost('password'))
					->save();
			}
			else {
				Bootstrap::log($post);
				if (is_array($errors)) {
					foreach ($errors as $errorMessage) {
						$session->addError($errorMessage);
					}
				} else {
					$session->addError($this->__('Invalid user data'));
				}
			}
		}

		$this->view->form = $form;
	}

	// @TODO last login date
	// @TODO create error messages
	public function loginAction()
	{
		/*$session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('/u/cbourdage');
            return;
        }*/

		$form = new Elm_Model_User_Form_Login();
		if ($this->getRequest()->isPost()) {
			$post = $this->getRequest()->getPost();
			$user = Bootstrap::getModel('user');
			if ($form->isValid($post)) {
				$user->setPassword($this->getRequest()->getPost('password'));
				Bootstrap::log($user);
				die('logging in: ');
				$user->save();
			}
			else {
				Bootstrap::log($post);
			}
		}

		$this->view->form = $form;
	}

	/**
	 * Logout action request
	 * 
	 * @return void
	 */
	public function logoutAction()
	{
		$this->_getSession()->logout()->setBeforeAuthUrl($this->getCurrentUrl());
        $this->_redirect('/');
	}
}