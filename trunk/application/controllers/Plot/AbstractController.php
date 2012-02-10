<?php

require_once 'controllers/AbstractController.php';

class Elm_Plot_AbstractController extends Elm_AbstractController
{
	public function init()
	{
		$layout = $this->_helper->layout();
		$layout->setLayout('two-column');
	}

	protected function _initLayout()
	{
		$this->view->placeholder('sidebar')->set($this->view->render('plot/_sidebar.phtml'));
	}

	protected function _getSession()
	{
		return Bootstrap::getSingleton('user/session');
	}
}