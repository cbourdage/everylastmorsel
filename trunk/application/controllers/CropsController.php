<?php

require_once 'controllers/Profile/AbstractController.php';

class Elm_CropsController extends Elm_Profile_AbstractController
{
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
		$this->view->headTitle()->prepend('My Crops');
		$this->_initLayout();
	}

	/**
	 *
	 */
	public function addAction()
	{
		//$this->_
		if ($this->getRequest()->isPost()) {
			try {
				$data = $this->getRequest()->getPost();
				Elm::log($data);
				die('deaddd');
				$crop = new Elm_Model_Crop();
				$crop->setData($data);
				if ($crop->isValid()) {
					$plot = Elm::getSingleton('plot')->load($this->getRequest()->getParam('plot_id'));
					$result = $plot->addCrop($crop);
					$this->_getSession()->addSuccess('Crop added');
				} else {
					$this->_getSession()->addError('Check all form fields are filled out.');
				}
			} catch (Exception $e) {
				$this->_getSession()->addError("Ah, we've run into an error. Try again");
				Elm::logException($e);
			}
		}
		$this->_redirect('/crops/');
	}
}