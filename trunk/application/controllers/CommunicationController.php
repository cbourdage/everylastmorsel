<?php

require_once 'controllers/AbstractController.php';

class Elm_CommunicationController extends Elm_AbstractController
{
	protected $_session;

	protected function _getSession()
	{
		if (!$this->_session) {
			$this->_session = Elm::getSingleton('user/session');
		}
		return $this->_session;
	}

	protected function _initAjax($auth = false)
	{
		$this->_helper->layout()->disableLayout();

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
			Elm::profile('communication_send', 'start');
			if ($message->send()) {
				Elm::profile('communication_send', 'end');
				$this->_helper->json->sendJson(array(
					'success' => true,
					'error' => false,
					'message' => "You're message has been successfully sent!"
				));
			} else {
				$this->_helper->json->sendJson(array(
					'success' => true,
					'error' => false,
					'message' => "You're message has been successfully sent!"
				));
				$this->_helper->json->sendJson(array(
					'success' => false,
					'error' => true,
					'message' => 'Error sending message at this time. We sincerely apologize.'
				));
			}
		}  else {
			$this->_helper->json->sendJson(array(
				'success' => false,
				'error' => true,
				'message' => 'Check the form is filled out.'
			));
		}
	}
}