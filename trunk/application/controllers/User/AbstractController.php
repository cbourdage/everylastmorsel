<?php

class Elm_User_AbstractController extends Colony_Controller_Action
{
	protected $_session;

	public function init()
	{
		$action = $this->getRequest()->getActionName();
        $pattern = '/^(create|login)/i';
        if (!preg_match($pattern, $action)) {
         	$layout = $this->_helper->layout();
			$layout->setLayout('two-column');
        }
	}

	protected function _getSession()
	{
		if (!$this->_session) {
			$this->_session = Bootstrap::getSingleton('user/session');
		}
		return $this->_session;
	}

	/**
	 * Initializes the User layout objects
	 */
	protected function _initLayout()
	{
		$this->view->placeholder('sidebar')->set($this->view->render('user/_sidebar.phtml'));
	}
}