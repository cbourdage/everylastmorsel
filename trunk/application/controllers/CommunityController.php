<?php

/**
 * Elm_CommunityController
 *
 * @TODO noRoute action
 */
class Elm_CommunityController extends Colony_Controller_Action
{

	protected function _initAjax()
	{
		$this->_helper->layout()->disableLayout();
	}

	/**
	 * @return void
	 */
    public function indexAction()
    {
		$model = Bootstrap::getSingleton('community');
		$this->view->users = $model->getUsers();
		$this->view->plots = $model->getPlots();
    }
}

