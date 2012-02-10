<?php

/**
 * Elm_CommunityController
 */
require_once 'controllers/AbstractController.php';

class Elm_CommunityController extends Elm_AbstractController
{
	/**
	 * @return void
	 */
    public function indexAction()
    {
		$model = Bootstrap::getSingleton('community');
		$this->view->users = $model->getUsers();
		$this->view->plots = $model->getPlots();
    }

	/**
	 * Default 404
	 */
	public function noRouteAction()
	{
	}
}

