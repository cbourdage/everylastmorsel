<?php

require_once 'controllers/AbstractController.php';

class Elm_CommunicationController extends Elm_AbstractController
{
	protected $_session;

	protected function _getSession()
	{
		if (!$this->_session) {
			$this->_session = Bootstrap::getSingleton('user/session');
		}
		return $this->_session;
	}

	protected function _initAjax($auth = false)
	{
		$this->_helper->layout()->disableLayout();

		if ($auth !== false) {
			if (!Bootstrap::getSingleton('user/session')->isLoggedIn()) {
				$this->_helper->json->sendJson(array(
					'success' => false,
					'error' => true,
					'location' => $this->view->url('user/login')
				));
			}
		}
	}

	public function sendAction()
	{
		$this->_initAjax(true);

		if ($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getParams();
			$form = new Elm_Model_Form_Communication_Contact();
			if ($form->isValid($data)) {
				$message = Bootstrap::getModel('communication')->init($data);
				if ($message->send()) {
					$this->_helper->json->sendJson(array(
						'success' => true,
						'error' => false,
						'message' => 'Successfully sent your message along!'
					));
				} else {
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
}