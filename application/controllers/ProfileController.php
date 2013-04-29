<?php

require_once 'controllers/Profile/AbstractController.php';

class Elm_ProfileController extends Elm_Profile_AbstractController
{
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
		$this->_forward('about');
	}

	/**
	 * About action
	 */
	public function aboutAction()
	{
		$this->_init();
		$this->_initLayout();
	}

    /**
     * About action
     */
    public function plotsAction()
    {
        $this->_init();
        $this->_initLayout();
    }


    /**
	 * Login action
	 *
	 * @return void
	 */
	public function loginAction()
	{
		$session = $this->_getSession();
        if ($session->isLoggedIn()) {
			$this->_redirect('/profile/');
            return;
        }

		$form = new Elm_Model_Form_User_Login();
		if ($session->formData) {
			$form->setDefaults($session->formData);
			$session->formData = null;
		}

		$this->view->headTitle()->prepend('Account Login');
		$this->view->form = $form;
	}

	/**
	 * Login post action
	 *
	 * @return mixed
	 */
	public function loginPostAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->_redirect('/profile/login');
			return;
		}

		$form = new Elm_Model_Form_User_Login();
		$post = $this->getRequest()->getParams();
		$session = $this->_getSession();

		if ($form->isValid($post)) {
			try {
				$session->login($post['email'], $post['password']);
				if (preg_match('/(logout)/i', $session->beforeAuthUrl)) {
					$session->beforeAuthUrl = '/profile/';
				}
				$this->_redirect($session->beforeAuthUrl);
				return;
			} catch (Colony_Exception $e) {
				$session->addError($e->getMessage());
				Elm::logException($e);
			} catch (Exception $e) {
				$session->addError($e->getMessage());
				Elm::logException($e);
			}
		} else {
			$session->formData = $post;
			$session->addError('Login and password are required.');
		}

		$this->_redirect('/profile/login');
	}

	/**
	 * Login ajax action
	 *
	 * @return void
	 */
	public function loginAjaxAction()
	{
		$this->_initAjax();
		$this->getHelper()->viewRenderer->setNoRender(true);

		$request = $this->getRequest();
		$response = array();
		$session = $this->_getSession();
        if ($session->isLoggedIn()) {
			$response = array(
				'success' => true,
				'error' => false,
				'location' => $request->getParam('after_auth') ? $request->getParam('after_auth') : $this->getUrl('profile')
			);
        } else {
			$form = new Elm_Model_Form_User_Login();
			$post = $this->getRequest()->getPost();
			if ($this->getRequest()->isPost() && $form->isValid($post)) {
				try {
					$session->login($post['email'], $post['password']);
					$response = array(
						'success' => true,
						'error' => false,
						'location' => $request->getParam('after_auth') ? $request->getParam('after_auth') : $this->getUrl('profile')
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
					'message' => 'Login and password are required.'
				);
			}
		}

		$this->_helper->json->sendJson($response);
	}

	/**
	 * Ajax authentication method for overlay
	 */
	public function authenticateAction()
	{
		$this->_initAjax();
		$this->_helper->viewRenderer->setNoRender(true);

		if (Elm::getSingleton('user/session')->isLoggedIn()) {
			$response = array(
				'success' => true,
				'error' => false
			);
			$this->_helper->json->sendJson($response);
		} else {
			$response = array(
				'success' => false,
				'error' => true,
				'location' => $this->view->url('plot/create')
			);
			$this->_helper->json->sendJson($response);
		}

		//$this->getResponse()->sendResponse();
	}

	/**
	 * Logout action request
	 *
	 * @return void
	 */
	public function logoutAction()
	{
		$_session = $this->_getSession();
		$_session->logout();
		$_session->beforeAuthUrl = $this->getCurrentUrl();
        $this->_redirect('/');
	}

	/**
	 * Registration action
	 *
	 * @return void
	 */
	public function createAction()
	{
		$session = $this->_getSession();
        if ($session->isLoggedIn()) {
			$this->_redirect('/profile/');
            return;
        }

		$form = new Elm_Model_Form_User_Create();
		$form->setAction('/profile/create-post');

		// Set data if stored in session b/c of an error
		if ($location = Elm::getSingleton('session')->location) {
			$form->setDefaults(array(
				'city' => $location->getCity(),
				'state' => $location->getState(),
				'zipcode' => $location->getZip()
			));
		}

		if ($session->formData) {
			$form->setDefaults($session->formData);
			$session->formData = null;
		}

		$this->view->headTitle()->prepend('Create Account');
		$this->view->form = $form;
	}

	/**
	 * registration post action
	 *
	 * @return mixed
	 */
	public function createPostAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->_redirect('/profile/create');
			return;
		}

		$form = new Elm_Model_Form_User_Create();
		$post = $this->getRequest()->getParams();
		$session = $this->_getSession();

		if ($form->isValid($post)) {
            try {
                $user = Elm::getModel('user');
                $user->setData($post)->setPassword($post['password']);
                $user->setIsConfirmed(true); // temp fix
                $user->save();

                // Increment inviteCode
                $inviteCode = Elm::getSingleton('inviteCode')->load($post['invite_code']);
                $inviteCode->increment()->save();

                // setup session, send email, add messages, move on
                $session->setUserAsLoggedIn($user);
                $session->isJustRegistered = true;
                $user->sendNewAccountEmail($session->beforeAuthUrl);
                $session->addSuccess(sprintf("Glad to have you on board, %s!", $user->getFirstname()));
                $this->_redirect('/profile/');
                return;

                // Use for account confirmation process
                $session->isJustRegistered = true;
                $user->sendNewAccountEmail($session->beforeAuthUrl);
                $this->_redirect('/profile/confirmation');
                return;
            } catch (Colony_Exception $e) {
                $session->addError($e->getMessage());
                Elm::logException($e);
            } catch (Exception $e) {
                //$session->addError($e->getMessage());
                $session->addError('Gah, there was an error processing your form. Please try again shortly.');
                Elm::logException($e);
            }
		} else {
			$session->formData = $post;
			$session->addError('Check all fields are filled out correctly.');
		}

		$this->_redirect('/profile/create');
	}

	/**
	 *
	 */
	public function confirmationAction()
	{
		$session = $this->_getSession();
		if ($key = $this->getRequest()->getParam('uid', null)) {
			//die($key);
			$user = new Elm_Model_User();
			if ($user->checkConfirmationKey($key)) {
				if (!$user->getIsConfirmed()) {
					$user->setIsConfirmed(true)->save();
					$user->sendConfirmedAccountEmail();

					//$session->setUserAsLoggedIn($user);
					//$session->addSuccess("Excellent! Let's get growin'.");
					$this->_forward('confirmed');
				} else {
					$this->_redirect('profile');
				}
			} else {
				$this->_getSession()->addError('Whoops. This key does not match anything on file!');
				$this->_forward('no-route');
			}
			return;
		}

		if (!$session->isJustRegistered) {
			$this->_redirect('/profile');
			return;
		}
	}

	/**
	 *
	 */
	public function confirmedAction()
	{
		$session = $this->_getSession();
        if ($session->isLoggedIn()) {
			$this->_redirect('/profile/');
            return;
        }
	}

	/**
	 * Password reset action request
	 *
	 * @return void
	 */
	public function forgotPasswordAction()
	{
		$_session = $this->_getSession();

		if ($this->getRequest()->isPost()) {
			$email = $this->getRequest()->getParam('email');
			if (!Zend_Validate::is($email, 'EmailAddress')) {
				$_session->addError('Invalid email address');
				$_session->forgotPasswordEmail = $email;
			} else {
				$user = Elm::getSingleton('user')->loadByEmail($email);
				if ($user->getId()) {
					try {
						$newPassword = $user->generatePassword();
						$user->changePassword($newPassword);
						$user->sendPasswordResetEmail();
						$_session->addSuccess('A new password has been sent.');
						$_session->forgotPasswordEmail = null;
					} catch (Exception $e) {
						$_session->addError($e->getMessage());
						$_session->forgotPasswordEmail = $email;
					}
				} else {
					$_session->addError('This email address was not found in our records.');
                	$_session->forgotPasswordEmail = $email;
				}
			}
		}

		$this->view->headTitle()->prepend('Forgot Password');
	}

	/**
	 * Users info page
	 *
	 * @return mixed
	 */
	public function infoAction()
	{
		$session = $this->_getSession();
		if (!$session->isLoggedIn()) {
			$this->_redirect('/profile/login');
			return;
		}

		$this->_init();

		$this->view->headTitle()->prepend('Info');
		$this->view->headTitle()->prepend($session->user->getFirstname() . ' ' . $session->user->getLastname());

		$form = new Elm_Model_Form_User_Info();
		$form->setAction('/profile/info-save');

		// Set data if stored in session b/c of an error
		if ($session->formData) {
			$form->setDefaults($session->formData);
			$session->formData = null;
		}

		$this->view->form = $form;
	}

	/**
	 * Users info save action
	 *
	 * @return mixed
	 */
	public function infoSaveAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->_redirect('/profile/info');
			return;
		}

		$this->_init();
		$session = $this->_getSession();

		$form = new Elm_Model_Form_User_Info();
		$post = $this->getRequest()->getParams();

		if ($form->isValid($post)) {
			try {
				$user = $session->getUser();
				$user->addData($post)->save();
				// Upload image
				Elm::getModel('user/image')->upload($session->user, $post);
				$session->addSuccess('Successfully saved your info!');
			} catch (Colony_Exception $e) {
				$session->addError($e->getMessage());
			} catch (Exception $e) {
				$session->addError($e->getMessage());
			}
		} else {
			unset($post['image']);
			$session->formData = $post;
			$session->addError('Check all fields are filled out correctly.');
		}

		$this->_redirect('/profile/info');
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
			$this->_redirect('/profile/login');
			return;
		}

		$this->_init();

		$this->view->headTitle()->prepend('Settings');
		$this->view->headTitle()->prepend($session->user->getFirstname() . ' ' . $session->user->getLastname());

		$form = new Elm_Model_Form_User_Settings();
		$form->setAction('/profile/settings-save');

		// Set data if stored in session b/c of an error
		if ($session->formData) {
			$form->setDefaults($session->formData);
			$session->formData = null;
		}

		$this->view->form = $form;
	}

	/**
	 * Users settings save action
	 *
	 * @return mixed
	 */
	public function settingsSaveAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->_redirect('/profile/settings');
			return;
		}

		$this->_init();
		$session = $this->_getSession();

		$form = new Elm_Model_Form_User_Settings();
		$post = $this->getRequest()->getParams();
		if ($form->isValid($post)) {
			try {
				/** @var $user Elm_Model_User */
				$user = $session->getUser();
				$user->addData($post);

				if ($post['password']) {
					$user->changePassword($post['password']);
				}

				$user->save();
				$session->addSuccess('Successfully saved your settings!');
			} catch (Colony_Exception $e) {
				$session->addError($e->getMessage());
			} catch (Exception $e) {
				$session->addError($e->getMessage());
			}
		} else {
			$session->formData = $post;
			$session->addError('Check all fields are filled out!');
		}

		$this->_redirect('/profile/settings');
	}

	/**
	 * User image upload action
	 */
	public function imageUploadAction()
	{
		if ($this->getRequest()->isPost()) {
			Elm::getModel('user/image')->upload(
				Elm::getModel('user')->load($this->_getSession()->getUser()->getId('u')),
				$this->getRequest()->getParams()
			);
		}
		$this->_redirect('/profile/');
	}

	public function emailTestingAction()
	{
		$this->_init();
		$this->_helper->viewRenderer->setNoRender(true);

		// email testing
		//$this->_user->sendPasswordResetEmail();
		//$plot = Elm::getModel('plot')->load(2);
		//$plot->sendNewPlotEmail();
	}
}