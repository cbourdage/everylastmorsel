<?php

require_once 'controllers/Profile/AbstractController.php';

class Elm_PlotsController extends Elm_Profile_AbstractController
{
	/**
	 * Pre Dispatch check for invalid session
	 */
	public function preDispatch()
	{
        parent::preDispatch();

        $action = $this->getRequest()->getActionName();
		if (!$this->_getSession()->authenticate($this)) {
			$this->_redirect('/profile/login');
		}
	}

	/**
	 * Default 404
	 */
	public function noRouteAction()
	{
	}

	/**
	 * view action
	 */
	public function indexAction()
	{
		$this->_forward('view');
	}

	/**
	 * view action
	 */
	public function viewAction()
	{
		$this->_init();
		$this->view->headTitle()->prepend('My Plots');
		$this->_initLayout();
	}
}