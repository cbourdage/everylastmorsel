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

	protected function _initAjax()
	{
		$this->getHelper()->layout()->disableLayout();
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

	public function preDispatch()
	{
		if ($this->getRequest()->isXmlHttpRequest()) {
			if (!Elm::getSingleton('user/session')->isLoggedIn()) {
				$this->getHelper()->json->sendJson(array(
					'success' => false,
					'error' => true,
					'location' => $this->view->url('profile/login')
				));
			}
		} else {
			parent::preDispatch();
		}

		return $this;
	}

	/**
	 * main view action
	 */
	public function viewAction()
	{
		$this->_initAjax();
		$this->_init();

		if ($commId = $this->getRequest()->getParam('cid')) {
			$this->view->message = Elm::getModel('communication')->load($commId);
			$this->getHelper()->json->sendJson(array(
				'success' => true,
				'error' => false,
				'update_areas' => array(
					'content',
				),
				'html' => array(
					'content' => $this->view->partial('communication/view.phtml', array('message' => $this->view->message)),
				)
			));
		} else {
			$this->_forward('retrieve');
		}

	}

	/**
	 * main view action
	 */
	public function listAction()
	{
		$this->_init();

		// Check if ajax/type retrieve request
		if ($this->getRequest()->getParam('type')) {
			$this->_forward('retrieve');
			return;
		}

		$this->view->headTitle()->append('Communication Hub');
		$comm = Elm::getModel('communication')->setUserId($this->_user->getId())
			->setFilterBy('inbox');
		$this->view->messages = $comm->retrieve();
		$this->view->type = 'Inbox';
	}

	/**
	 * retrieve by type ('sent', 'inbox', 'archive')
	 */
	public function retrieveAction()
	{
		$this->_initAjax();
		$this->_init();

		$type = $this->getRequest()->getParam('type', 'inbox');
		$comm = Elm::getModel('communication')->setUserId($this->_user->getId())
			->setFilterBy($type);
		$this->view->messages = $comm->retrieve();
		$this->view->type = ucfirst($type);

		$this->getHelper()->json->sendJson(array(
			'success' => true,
			'error' => false,
			'update_areas' => array(
				'content',
			),
			'html' => array(
				'content' => $this->view->partial('communication/list.phtml', array(
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
		$this->_initAjax();
		$data = $this->getRequest()->getParams();
		$form = new Elm_Model_Form_Communication_Contact();
		if ($form->isValid($data)) {
			$message = Elm::getModel('communication')->init($data);
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

	/**
	 * @return mixed
	 */
	public function replyAction()
	{
		$this->_initAjax();
		$data = $this->getRequest()->getParams();
		$message = Elm::getModel('communication')->init($data);
		if ($message->isValidReply($data)) {
			if ($message->reply()) {
				$this->getHelper()->json->sendJson(array(
					'success' => true,
					'error' => false,
					'message' => "You're message has been successfully sent!"
				));
			} else {
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

	/**
	 * Archive action
	 */
	public function archiveAction()
	{
		if ($commId = $this->getRequest()->getParam('cid')) {
			$message = Elm::getModel('communication')->load($commId)->archive();
		}

		$this->_forward('retrieve');
	}

	/**
	 * mark-read action
	 */
	public function markReadAction()
	{
		if ($commId = $this->getRequest()->getParam('cid')) {
			$message = Elm::getModel('communication')->load($commId)
				->setIsRead(true)
				->save();
		}

		$this->_forward('retrieve');
	}

	/**
	 * Delete action
	 */
	public function deleteAction()
	{
		if ($commId = $this->getRequest()->getParam('cid')) {
			//$message = Elm::getModel('communication')->load($commId)->delete();
		}

		$this->_forward('retrieve');
	}
}