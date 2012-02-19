<?php

require_once 'controllers/AbstractController.php';

class Elm_Plot_AbstractController extends Elm_AbstractController
{
	protected $_plot = null;

	public function init()
	{
		$layout = $this->_helper->layout();
		$layout->setLayout('two-column');
	}

	protected function _isValid()
	{
		if (!$id = $this->getRequest()->getParam('p')) {
			return false;
		}

		if (!$plot = Bootstrap::getModel('plot')->load($id)) {
			return false;
		}

		return true;
	}

	protected function _initCurrentPlot()
	{
		$this->_plot = Bootstrap::getModel('plot')->load($this->getRequest()->getParam('p'));
		return $this;
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