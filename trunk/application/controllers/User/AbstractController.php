<?php

require_once 'controllers/AbstractController.php';

class Elm_User_AbstractController extends Elm_AbstractController
{
	protected $_session;

	protected $_user;

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

	protected function _initCurrentUser()
	{
		$this->_user= Bootstrap::getModel('user')->loadByAlias($this->getRequest()->getParam('alias'));
		Zend_Registry::set('current_plot', $this->_user);
		return $this;
	}

	/**
	 * Initializes the User layout objects
	 */
	protected function _initLayout()
	{
		$this->view->placeholder('contact-modal')->set($this->view->render('communication/_modal.phtml'));
		$this->view->placeholder('sidebar')->set($this->view->render('user/_sidebar.phtml'));
	}
}