<?php

class Elm_Plot_AbstractController extends Colony_Controller_Action
{
	public function init()
	{
		$layout = $this->_helper->layout();
		$layout->setLayout('two-column');
	}

	protected function _getSession()
	{
		return Bootstrap::getSingleton('user/session');
	}
}