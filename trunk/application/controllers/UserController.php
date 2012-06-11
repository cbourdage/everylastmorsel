<?php

require_once 'controllers/User/AbstractController.php';

class Elm_UserController extends Elm_User_AbstractController
{
	/**
	 * Default 404
	 */
	public function noRouteAction()
	{
		$layout = $this->_helper->layout();
		$layout->setLayout('main');
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
		//$userAlias = $this->getRequest()->getParam('alias');
		//$user = Elm::getModel('user')->loadByAlias($userAlias);
		$userId = $this->getRequest()->getParam('u');
		$user = Elm::getModel('user')->load($userId);
		if ($user->getId()) {
			$this->view->user = $user;
			Zend_Registry::set('current_user', $user);
			$this->view->headTitle()->prepend($user->getFirstname() . ' ' . $user->getLastname());
		} else {
			$this->_forward('no-route');
		}

		$this->_initLayout();
	}
}