<?php

require_once 'controllers/Profile/AbstractController.php';

class Elm_CommunicationController extends Elm_Profile_AbstractController
{
	protected $_session;

	protected function _init()
	{
		parent::_init();
		$this->_initLayout();
	}

	protected function _getSession()
	{
		if (!$this->_session) {
			$this->_session = Elm::getSingleton('user/session');
		}
		return $this->_session;
	}

	protected function _initAjax($auth = false)
	{
		$this->getHelper()->layout()->disableLayout();

		if ($auth !== false) {
			if (!Elm::getSingleton('user/session')->isLoggedIn()) {
				$this->_helper->json->sendJson(array(
					'success' => false,
					'error' => true,
					'location' => $this->view->url('user/login')
				));
			}
		}
	}

	/**
	 * Initializes the User layout objects
	 */
	protected function _initLayout()
	{
		$layout = $this->getHelper()->layout();
		$layout->setLayout('two-column');
		$this->view->placeholder('sidebar')->set($this->view->render('communication/_sidebar.phtml'));
	}

	/**
	 * main view action
	 */
	public function viewAction()
	{
		$this->_init();

		// Check if ajax/type retrieve request
		if ($this->getRequest()->getParam('type')) {
			Elm::log('retrieving');
			$this->_forward('retrieve');
			return;
		}

		$this->view->headTitle()->append('Communication Hub');
		$this->view->messages = Elm::getModel('communication')->getByUserId($this->_user->getId());

		$this->view->type = 'Inbox';
		//$this->_initLayout();
	}

	/**
	 * retrieve by type ('sent', 'inbox', 'archive')
	 */
	public function retrieveAction()
	{
		$this->_initAjax(true);
		$this->_init();

		$type = $this->getRequest()->getParam('type');
		$this->view->messages = Elm::getModel('communication')->getByUserId($this->_user->getId());
		$this->view->type = ucfirst($type);

		$this->getHelper()->json->sendJson(array(
			'success' => true,
			'error' => false,
			'update_areas' => array(
				'content',
			),
			'html' => array(
				'content' => $this->view->partial('communication/view.phtml', array(
					'messages' => $this->view->messages,
					'type' => $this->view->type
				)),
			)
		));
	}

	/**
	 * @return mixed
	 */
	public function sendAction()
	{
		$this->_initAjax(true);
		if (!$this->getRequest()->isPost()) {
			return;
		}

		$post = $this->getRequest()->getParams();
		$form = new Elm_Model_Form_Communication_Contact();
		if ($form->isValid($post)) {
			$message = Elm::getModel('communication')->init($post);
			//Elm::profile('communication_send', 'start');
			if ($message->send()) {
				//Elm::profile('communication_send', 'end');
				$this->getHelper()->json->sendJson(array(
					'success' => true,
					'error' => false,
					'message' => "You're message has been successfully sent!"
				));
			} else {
				$this->getHelper()->json->sendJson(array(
					'success' => true,
					'error' => false,
					'message' => "You're message has been successfully sent!"
				));
				$this->getHelper()->json->sendJson(array(
					'success' => false,
					'error' => true,
					'message' => 'Error sending message at this time. We sincerely apologize.'
				));
			}
		}  else {
			$this->getHelper()->json->sendJson(array(
				'success' => false,
				'error' => true,
				'message' => 'Check the form is filled out.'
			));
		}
	}
}