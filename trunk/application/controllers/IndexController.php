<?php

/**
 * Elm_IndexController
 */
require_once 'controllers/AbstractController.php';

class Elm_IndexController extends Elm_AbstractController
{
	/**
	 * initializes layout for ajax requests
	 */
	protected function _initAjax()
	{
		$this->_helper->layout()->disableLayout();
	}

	/**
	 * @return void
	 */
    public function indexAction()
    {
    }

	/**
	 * Help Action
	 *
	 * @return void
	 */
	public function helpAction()
	{
	}

	/**
	 * FAQ page
	 *
	 * @return void
	 */
	public function faqAction()
	{
	}

	/**
	 * Ajax Step 1 to plot the point and store data
	 */
	public function plotPointAction()
	{
		$this->_initAjax();
		//Bootstrap::log($this->getRequest()->getParams());
		Bootstrap::getSingleton('user/session')->plot = array(
			'latitude' => $this->getRequest()->getParam('lat'),
			'longitude' => $this->getRequest()->getParam('long')
		);
		$this->getResponse()->sendResponse();
	}

	/**
	 * Ajax authentication method for overlay
	 */
	public function authenticateAction()
	{
		$this->_initAjax();
		Bootstrap::getSingleton('user/session')->plot['type'] = $this->getRequest()->getParam('type');
		if (Bootstrap::getSingleton('user/session')->isLoggedIn()) {
			if ($this->getRequest()->getParam('type') == 'isA') {
				$this->_forward('garden-details');
			} else {
				$this->_forward('garden-details');
				/*$response = array(
					'success' => true,
					'error' => false,
					'location' => $this->view->url('plot/startup')
				);
				$this->_helper->json->sendJson($response);*/
			}
		} else {
			$loginForm = new Elm_Model_Form_User_Login();
			$loginForm->setAction('/user/login-ajax');
			$this->view->loginForm = $loginForm;

			$createForm = new Elm_Model_Form_User_Create();
			$createForm->setAction('/user/create-ajax');
			$createForm->removeElement('location');
			$this->view->createForm = $createForm;
		}
		$this->getResponse()->sendResponse();
	}

	/**
	 * Ajax step 3 for when plot is a garden
	 */
	public function gardenDetailsAction()
	{
		$this->_initAjax();
		$form = new Elm_Model_Form_Plot_Create();
		$form->setAction('/plot/create-ajax');
		$form->removeElement('submit');
		$this->view->form = $form;
	}
}

