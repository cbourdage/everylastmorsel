<?php

require_once 'controllers/Profile/AbstractController.php';

class Elm_YieldsController extends Elm_Profile_AbstractController
{
	/**
	 * initializes layout for ajax requests
	 */
	protected function _initAjax()
	{
		$this->_helper->layout()->disableLayout();
	}

	/**
	 * Default 404
	 */
	public function noRouteAction()
	{
	}

	/**
	 * @return mixed
	 */
	public function addPostAction()
	{
		//if ($this->getRequest()->getParam('isAjax')) {
		$this->_initAjax();
		$response = array();

		if (!$this->getRequest()->isPost()) {
			$this->_helper->json->sendJson(array('success' => true, 'location' => $this->_helper->url('crops')));
			return;
		}

		$post = $this->getRequest()->getPost();
		$form = new Elm_Model_Form_Yield_Add();
		if ($form->isValid($post)) {
			try {
				$yield = new Elm_Model_Yield();
				$yield->prepareNewYield($post);
				$response = array(
					'success' => true,
					'message' => 'Successfully added yield.'
				);
			} catch (Exception $e) {
				Elm::logException($e);
				$response = array('error' => true, 'message' => $e->getMessage());
			}
		} else {
			$response = array(
				'error' => true,
				'message' => 'Check all form fields are filled out.'
			);
		}

		$this->_helper->json->sendJson($response);
	}

	/**
	 * @return mixed
	 */
	public function updatePostAction()
	{
		$this->_initAjax();
		$response = array();

		if (!$this->getRequest()->isPost()) {
			$this->_helper->json->sendJson(array('success' => true, 'location' => $this->_helper->url('crops')));
			return;
		}

		$post = $this->getRequest()->getPost();
		try {
			$yield = new Elm_Model_Yield();
			$yield->prepareYieldUpdates($post);
			$response = array(
				'success' => true,
				'message' => 'Successfully added yield.'
			);
		} catch (Exception $e) {
			Elm::logException($e);
			$response = array('error' => true, 'message' => $e->getMessage());
		}

		$this->_helper->json->sendJson($response);
	}

	/**
	 * @return mixed
	 */
	public function sellYieldPostAction()
	{
		$this->_initAjax();
		$response = array();

		if (!$this->getRequest()->isPost()) {
			$this->_helper->json->sendJson(array('success' => true, 'location' => $this->_helper->url('crops')));
			return;
		}

		$data = $this->getRequest()->getParam('purchasable');
		$form = new Elm_Model_Form_Yield_Sell();
		if ($form->isValid($data)) {
			try {
				$yield = Elm::getModel('yield')->load($data['yield_id']);
				$yield->makePurchasable($data);
				$response = array(
					'success' => true,
					'message' => 'Successfully put ' . $data['quantity'] . ' up for sale.'
				);
			} catch (Exception $e) {
				Elm::logException($e);
				$response = array('error' => true, 'message' => $e->getMessage());
			}
		} else {
			$response = array(
				'error' => true,
				'message' => 'Check all form fields are filled out.'
			);
		}

		$this->_helper->json->sendJson($response);
	}

	/**
	 * @return mixed
	 */
	public function cancelForSaleAction()
	{
		if ($yieldId = $this->getRequest()->getParam('yield_id')) {
			try {
				$yield = Elm::getModel('yield')->load($yieldId);
				$yield->cancelPurchasable();
				// @TODO add to session messages
			} catch (Exception $e) {
				Elm::logException($e);
				// @TODO add to session messages
			}
		}

		$this->_redirect($this->getRequest()->getHeader('referer'));
	}
}