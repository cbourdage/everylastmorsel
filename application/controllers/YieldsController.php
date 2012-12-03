<?php

require_once 'controllers/Profile/AbstractController.php';

class Elm_YieldsController extends Elm_Profile_AbstractController
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
		$this->_redirect('/crops');
		return;

		$this->_init();
		$this->view->headTitle()->prepend('My Crops');
		$this->_initLayout();
	}

	/**
	 *
	 */
	public function addAction()
	{

	}

	/**
	 * @return mixed
	 */
	public function addPostAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->_redirect('/crops/');
			return;
		}

		$post = $this->getRequest()->getPost();
		$plot = Elm::getSingleton('crop')->load($post['crop_id']);

		$form = new Elm_Model_Form_Yield();
		if ($form->isValid($post)) {
			try {
				$yield = new Elm_Model_Yield();
				$yield->extractData($post);
				$yield->save();
				$yield->createNewYieldStatus();
				$this->_getSession()->addSuccess('Alright! Yield added to ' . $yield->getCropName());
			} catch (Exception $e) {
				Elm::logException($e);
				$this->_getSession()->addError("Ah, we've run into an error. Try again");
			}
		} else {
			$this->_getSession()->formData = $post;
			$this->_getSession()->addError('Check all form fields are filled out.');
		}

		if (!($redirectUrl = $this->_getSession()->lastUrl)){
			$redirectUrl = '/crops/';
		}

		$this->_redirect($redirectUrl);
	}
}