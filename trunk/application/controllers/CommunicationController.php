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
				return;
			}
		}
	}

	public function sendAction()
	{
		$this->_initAjax(true);
		Bootstrap::log($this->getRequest()->getParams());

		if ($this->getRequest()->isPost()) {
			$message = Bootstrap::getModel('communication')->init($this->getRequest()->getParams());
			Bootstrap::log($message);
			$message->send();
			$this->_helper->json->sendJson(array(
				'success' => true,
				'error' => false,
				'message' => 'Successfully sent your message along!'
			));
			return;
		}
	}
}