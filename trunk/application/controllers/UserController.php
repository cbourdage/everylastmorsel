<?php

require_once 'controllers/User/AbstractController.php';

class Elm_UserController extends Elm_User_AbstractController
{
	/**
	 * Pre Dispatch check for invalid session
	 */
	public function preDispatch()
	{
        parent::preDispatch();

		// @TODO figure out redirects for login and registration pages - all pages for that matter.
        $action = $this->getRequest()->getActionName();
		Bootstrap::log($action);
        $pattern = '/^(create|login)/i';
        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
				Bootstrap::log(__METHOD__ . ' false');
                //$this->_redirect('/');
            }
        }
	}

	/**
	 * Post Dispatch
	 */
	public function postDispatch()
	{
		parent::postDispatch();
		if ($this->view->user) {
			Zend_Registry::set('current_user', $this->view->user);
		}
	}

	/**
	 * Initializes the User layout objects
	 */
	protected function _initLayout()
	{
		//$this->render('user/sidebar', 'sidebar', true);
		//$this->_helper->layout()->sidebar = '<h3>awesome</h3>';
		//$this->_helper->layout()->sidebar = $this->_helper->viewRenderer->render('user/sidebar', 'sidebar', false);
	}

	/**
	 * Initializes the layout for ajax requests
	 */
	protected function _initAjax()
	{
		$this->_helper->layout()->disableLayout();
	}

	/**
	 * Default 404
	 */
	public function noRouteAction()
	{
	}

	/**
	 * Index/view action
	 */
	public function indexAction()
	{
		$this->_forward('view');
	}

	/**
	 * Index/view action
	 */
	public function viewAction()
	{
		$this->_initLayout();
		$userAlias = $this->getRequest()->getParam('alias');
		$user = Bootstrap::getModel('user')->loadByAlias($userAlias);
		if ($user->getId()) {
			$this->view->user = $user;
			$this->view->headTitle()->prepend($user->getFirstname() . ' ' . $user->getLastname());
		} else {
			$this->_forward('no-route');
		}
	}

	/**
	 * Registration and registration post action
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

		$form = new Elm_Model_Form_User_Create();
		if ($this->getRequest()->isPost()) {
			$errors = array();
			$post = $this->getRequest()->getPost();
			$user = Bootstrap::getModel('user');
			if ($form->isValid($post)) {
				$user->setData($post)
					->setPassword($post['password']);
				$user->save();

				// setup session, send email, add messages, move on
				$session->setUserAsLoggedIn($user);
				$user->sendNewAccountEmail($session->beforeAuthUrl);
				$session->addSuccess(sprintf("Glad to have you on board, %s!", $user->getFirstname()));
				$url = '/u/' . $user->getAlias();
				$this->_redirect($url);
				return;
			}
			else {
				if (is_array($errors)) {
					foreach ($errors as $errorMessage) {
						$session->addError($errorMessage);
					}
				} else {
					$session->addError($this->__('Invalid user data'));
				}
			}
		}

		$this->view->headTitle()->prepend('Create Account');
		$this->view->form = $form;
	}

	/**
	 * Login and login post actions
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

		$form = new Elm_Model_Form_User_Login();
		if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if ($form->isValid($post)) {
                try {
                    $session->login($post['email'], $post['password']);
        			if (preg_match('/(logout)/i', $session->beforeAuthUrl)) {
						$session->beforeAuthUrl = '/u/' . $session->getUser()->getAlias();
					}
					Bootstrap::log($session->beforeAuthUrl);
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

		$this->view->headTitle()->prepend('Account Login');
		$this->view->form = $form;
	}

	/**
	 * Login ajax action
	 *
	 * @return void
	 */
	public function loginAjaxAction()
	{
		$this->_initAjax();
		$this->_helper->viewRenderer->setNoRender(true);

		$response = array();
		$session = $this->_getSession();
        if ($session->isLoggedIn()) {
			$response = array(
				'success' => true,
				'error' => false,
				'location' => $this->getUrl(null, array('alias' => $session->getUser()->getAlias(), '_route' => 'user'))
			);
        } else {
			$form = new Elm_Model_Form_User_Login();
			if ($this->getRequest()->isPost()) {
				$post = $this->getRequest()->getPost();
				if ($form->isValid($post)) {
					try {
						$session->login($post['email'], $post['password']);
						$response = array(
							'success' => true,
							'error' => false,
							'location' => $this->getUrl(null, array('alias' => $session->getUser()->getAlias(), '_route' => 'user'))
						);
					} catch (Exception $e) {
						$response = array(
							'success' => false,
							'error' => true,
							'message' => $e->getMessage()
						);
					}
				} else {
					$response = array(
						'success' => false,
						'error' => true,
						'message' =>'Login and password are required.'
					);
				}
			}
		}

		$this->_helper->json->sendJson($response);
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

	/**
	 * Users settings page
	 *
	 * @return mixed
	 */
	public function settingsAction()
	{
		$session = $this->_getSession();
		if (!$session->isLoggedIn()) {
			if ($session->beforeAuthUrl && $session->beforeAuthUrl != $this->getCurrentUrl()) {
				$this->_redirect($session->beforeAuthUrl);
			} else {
				$this->_redirect('/');
			}
			return;
		}

		$form = new Elm_Model_Form_User_Settings();
		if ($this->getRequest()->isPost()) {
			$post = $this->getRequest()->getParams();
			if ($form->isValid($post)) {
				try {
					$user = $session->user;
					$user->addData($post)->save();
					$session->addSuccess('Successfully saved your settings!');
				} catch (Colony_Exception $e) {
					$session->addError($e->getMessage());
				} catch (Exception $e) {
					$session->addError($e->getMessage());
				}

			} else {
				$session->addError('Check all fields are filled out!');
			}
		}

		$this->view->headTitle()->prepend('Settings');
		$this->view->headTitle()->prepend($session->user->getFirstname() . ' ' . $session->user->getLastname());
		$this->view->form = $form;
	}
}