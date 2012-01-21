<?php

/**
 * Elm_IndexController
 *
 * @TODO noRoute action
 */
class Elm_IndexController extends Colony_Controller_Action
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
    }

	public function plotPointAction()
	{
		$this->_initAjax();
		Bootstrap::getSingleton('user/session')->plot = array(
			'latitude' => $this->getRequest()->getParam('lat'),
			'longitude' => $this->getRequest()->getParam('long')
		);
		$this->getResponse()->sendResponse();
	}

	public function authenticateAction()
	{
		$this->_initAjax();
		Bootstrap::getSingleton('user/session')->plot['type'] = $this->getRequest()->getParam('type');
		if (Bootstrap::getSingleton('user/session')->isLoggedIn()) {
			if ($this->getRequest()->getParam('type') == 'isA') {
				$this->_forward('isgarden');
			} else {
				//$this->_forward('shouldbegarden');
				$response = array(
					'success' => true,
					'error' => false,
					'location' => $this->view->url('plot/startup')
				);
				$this->_helper->json->sendJson($response);
			}
		} else {
			$loginForm = new Elm_Model_User_Form_Login();
			$loginForm->setAction('/user/login-ajax');
			$this->view->loginForm = $loginForm;

			$createForm = new Elm_Model_User_Form_Create();
			$createForm->setAction('/user/create-ajax');
			$this->view->createForm = $createForm;
		}
		$this->getResponse()->sendResponse();
	}

	public function isgardenAction()
	{
		$this->_initAjax();
		$form = new Elm_Model_Plot_Form_Create();
		$form->setAction('/plot/create-ajax');
		$form->removeElement('submit');
		$this->view->form = $form;
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
}

