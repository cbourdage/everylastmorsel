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

		// @TODO figure out redirects for login and registration pages - all pages for that matter.
        $action = $this->getRequest()->getActionName();
        $pattern = '/^(create|login|forgotpassword)/i';
        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                //$this->_redirect('/');
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
		$userAlias = $this->getRequest()->getParam('alias');
		if ($user = Bootstrap::getModel('user')->loadByAlias($userAlias)) {
			$this->view->user = $user;
			$this->view->message = 'User account: ';
		}
		else {
			// forward to invalid
			$this->view->message = 'Invalid user account...';
		}
	}

	/**
	 * Registration and registration post action
	 *
	 * @TODO create error messages
	 * @TODO Error Validation
	 * 
	 * @return void
	 */
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
					->setPassword($this->getRequest()->getPost('password'));
				$user->save();

				// setup session, send email, add messages, move on
				$session->setUserAsLoggedIn($user);
				$user->sendNewAccountEmail($session->beforeAuthUrl);
				$session->addSuccess(sprintf("Glad to have you on board, %s!", $user->getFirstname()));
				if (!($url = $session->beforeAuthUrl)) {
					$url = '/u/' . $user->getAlias();
				}
				$this->_redirect($url);
				return;
			}
			else {
				/*if (is_array($errors)) {
					foreach ($errors as $errorMessage) {
						$session->addError($errorMessage);
					}
				} else {
					$session->addError($this->__('Invalid user data'));
				}*/
			}
		}

		$this->view->form = $form;
	}

	/**
	 * Login and login post actions
	 *
	 * @TODO last login date
	 * @TODO create error messages
	 *
	 * @return void
	 */
	public function loginAction()
	{
		$session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('/u/' . $session->getUser()->getAlias());
            return;
        }

		$form = new Elm_Model_User_Form_Login();
		if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if ($form->isValid($post)) {
                try {
                    $session->login($post['email'], $post['password']);
        			if (preg_match('/(logout)/i', $session->beforeAuthUrl)) {
						$session->beforeAuthUrl = '/u/' . $session->getUser()->getAlias();
					}
					$this->_redirect($session->beforeAuthUrl);
                } catch (Colony_Exception $e) {
                    $session->addError($e->getMessage());
                } catch (Exception $e) {
					$session->addError($e->getMessage());
                }
            } else {
                $session->addError('Login and password are required.');
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
		$this->_getSession()->logout()->beforeAuthUrl = $this->getCurrentUrl();
        $this->_redirect('/');
	}
}