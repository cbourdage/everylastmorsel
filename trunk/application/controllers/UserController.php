<?php

require_once 'controllers/User/AbstractController.php';

class Elm_UserController extends Elm_User_AbstractController
{
	public function preDispatch()
	{
		$userId = $this->getRequest()->getParam('u');
		$user = Elm::getModel('user')->load($userId);

		if ($user->getId()) {
			$this->view->user = $user;
			Zend_Registry::set('current_user', $user);
			$this->view->headTitle()->prepend($this->view->user->getFullname());
		}
	}

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
		if (!$this->view->user) {
			$this->_forward('no-route');
			return;
		}

		$this->_initLayout();
	}

	/**
	 * u/plots/id
	 */
	public function plotsAction()
	{
		if (!$this->view->user) {
			$this->_forward('no-route');
			return;
		}

		$this->view->headTitle()->append('Plots');
		$this->_initLayout();
	}

	/**
	 * u/crops/id
	 */
	public function cropsAction()
	{
		if (!$this->view->user) {
			$this->_forward('no-route');
			return;
		}

		$this->view->headTitle()->append('Crops');
		$this->_initLayout();
	}
}