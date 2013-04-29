<?php
/**
 * Individual user view
 *
 */
require_once 'controllers/User/AbstractController.php';

class Elm_UserController extends Elm_User_AbstractController
{
	/**
	 * Checks if the user id exists and then
	 * initializes the user object
	 */
	public function preDispatch()
	{
		parent::preDispatch();

        /**
         * @TODO check if private and forward to that privacyAction()
         */
        if ($userId = $this->getRequest()->getParam('u')) {
			$user = Elm::getModel('user')->load($userId);
			if ($user->getId()) {
				$this->_init();

				Elm::log($this->getCurrentUrl() . ' == ' . $this->_user->getUrl());
				if ($this->_user->isPrivate() && $this->_user->getUrl() !== $this->getCurrentUrl()) {
					Elm::log($this->_user->getId() . ' is private? ' . $this->_user->isPrivate());
					$this->_redirect($this->_user->getUrl());
					return;
				}
			}
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

    public function privacyAction()
    {
        $this->_initLayout();
    }

	/**
	 * Index/about action
	 */
	public function indexAction()
	{
		$this->_forward('about');
	}

    /**
     * Index/about action
     */
    public function aboutAction()
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