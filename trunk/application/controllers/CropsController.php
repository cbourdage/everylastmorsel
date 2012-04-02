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

		if ($this->_getSession()->formData) {
			$this->view->formData = $this->_getSession()->formData;
		} else {
			$this->view->formData = new Colony_Object(array(''));
		}
		$this->_initLayout();
	}

	/**
	 *
	 */
	public function addAction()
	{
		//$this->_
		if ($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost();
			$form = new Elm_Model_Form_Plot_Crop();
			if ($form->isValid($data)) {
				try {
					$plotCrop = new Elm_Model_Plot_Crop();
					$plotCrop->extractData($data);
					$plotCrop->save();
					$this->_getSession()->addSuccess('Crop added');
				} catch (Exception $e) {
					Elm::logException($e);
					$this->_getSession()->addError("Ah, we've run into an error. Try again");
				}
			} else {
				$this->_getSession()->formData = new Colony_Object($data);
				$this->_getSession()->addError('Check all form fields are filled out.');
			}
		}

		$this->_redirect('/crops/');
	}
}