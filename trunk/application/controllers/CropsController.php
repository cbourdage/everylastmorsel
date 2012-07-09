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
		if (!($plotId = $this->getRequest()->getParam('p', null))) {
			$this->_redirect('/crops/');
			return;
		}

		$session = $this->_getSession();
		$plot = Elm::getSingleton('plot')->load($plotId);

		$form = new Elm_Model_Form_Plot_Crop();
		if ($session->formData) {
			$form->setDefaults($session->formData);
			$session->formData = null;
		}

		$this->_init();
		$this->view->headTitle()->prepend('Add Crops');
		$this->view->form = $form;
		$this->view->plot = $plot;
		$this->_initLayout();
	}

	public function addPostAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->_redirect('/crops/');
			return;
		}

		$data = $this->getRequest()->getPost();
		$plot = Elm::getSingleton('plot')->load($data['plot_id']);

		$form = new Elm_Model_Form_Plot_Crop();
		if ($form->isValid($data)) {
			try {
				$plotCrop = new Elm_Model_Plot_Crop();
				$plotCrop->extractData($data);
				$plotCrop->save();
				$plotCrop->createNewCropStatus();
				$this->_getSession()->addSuccess('Successfully added a new crop to ' . $plot->getName());
			} catch (Exception $e) {
				Elm::logException($e);
				$this->_getSession()->addError("Ah, we've run into an error. Try again");
			}
		} else {
			$this->_getSession()->formData = new Colony_Object($data);
			$this->_getSession()->addError('Check all form fields are filled out.');
		}

		if (!($redirectUrl = $this->_getSession()->lastUrl)){
			$redirectUrl = '/crops/';
		}

		$this->_redirect($redirectUrl);
	}

	/**
	 * Auto-suggest search action
	 */
	public function searchAction()
	{
		$this->_initAjax();

		$results = array();
		$type = $this->getRequest()->getParam('type', null);
		$limit = $this->getRequest()->getParam('limit', null);
		if ($term = $this->getRequest()->getParam('term', null)) {
			$results = Elm::getModel('crop')->getVarieties($type, $term, $limit);
		}
		$this->_helper->json->sendJson($results);
	}

	/**
	 * test import action
	 */
	public function importAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		die('dead to me');
		$type = 'dry bean';
		$file = 'bean_dry_out.csv';
		Elm::getModel('crop')->import($file, $type);
		echo 'Completed: ' . $file;
	}
}