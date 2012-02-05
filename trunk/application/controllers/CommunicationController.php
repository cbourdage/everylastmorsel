<?php

class Elm_CommunicationController extends Colony_Controller_Action
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
			$message = Bootstrap::getModel('communication')->init($this->getRequest()->getParams());
			if ($message->send()) {
				$this->_helper->json->sendJson(array(
					'success' => true,
					'error' => false,
					'message' => 'Successfully sent your message along!'
				));
			} else {
				$this->_helper->json->sendJson(array(
					'success' => true,
					'error' => false,
					'message' => 'Error sending message at this time. We sincerely apologize.'
				));
			}
		}
	}
}