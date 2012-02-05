<?php

/**
 * Elm_CommunityController
 *
 */
class Elm_CommunityController extends Colony_Controller_Action
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

