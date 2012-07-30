<?php

/**
 * Elm_CommunityController
 */
require_once 'controllers/AbstractController.php';

class Elm_CommunityController extends Elm_AbstractController
{
	/**
	 * Default 404
	 */
	public function noRouteAction()
	{
	}

	/**
	 * @return void
	 */
    public function indexAction()
    {
		$model = Elm::getSingleton('community');
		$this->view->users = $model->getUsers();
    }


	/**
	 * @return void
	 */
    public function peopleAction()
    {
		$this->_forward('index');
    }

	/**
	 * @return void
	 */
    public function plotsAction()
    {
		$model = Elm::getSingleton('community');
		$this->view->plots = $model->getPlots();
    }
}

