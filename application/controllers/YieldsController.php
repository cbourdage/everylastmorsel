<?php

require_once 'controllers/Profile/AbstractController.php';

class Elm_YieldsController extends Elm_Profile_AbstractController
{
	/**
	 * initializes layout for ajax requests
	 */
	protected function _initAjax()
	{
		$this->getHelper()->layout()->disableLayout();
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
			$this->getHelper()->json->sendJson(array('success' => true, 'location' => $this->getHelper()->url('crops')));
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
					'message' => 'Successfully added yield.',
					'update_areas' => array(
						'crop-yields-' . $yield->getPlotCrop()->getId(),
						'crop-yield-actions-' . $yield->getPlotCrop()->getId(),
						'crop-yields-totals-' . $yield->getPlotCrop()->getId()
					),
					'html' => array(
						'crop-yields-' . $yield->getPlotCrop()->getId() => $this->view->partial('crops/yields/_list.phtml', array('pCrop' => $yield->getPlotCrop())),
						'crop-yield-actions-' . $yield->getPlotCrop()->getId() => $this->view->partial('crops/list/_actions.phtml', array('pCrop' => $yield->getPlotCrop())),
						'crop-yields-totals-' . $yield->getPlotCrop()->getId() => $this->view->partial('crops/yields/_totals.phtml', array('pCrop' => $yield->getPlotCrop())),
					)
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

		$this->getHelper()->json->sendJson($response);
	}

	/**
	 * @return mixed
	 */
	public function updatePostAction()
	{
		$this->_initAjax();
		$response = array();

		if (!$this->getRequest()->isPost()) {
			$this->getHelper()->json->sendJson(array('success' => true, 'location' => $this->getHelper()->url('crops')));
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

		$this->getHelper()->json->sendJson($response);
	}

	/**
	 * @return mixed
	 */
	public function sellYieldPostAction()
	{
		$this->_initAjax();
		$response = array();

		if (!$this->getRequest()->isPost()) {
			$this->getHelper()->json->sendJson(array('success' => true, 'location' => $this->getHelper()->url('crops')));
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
					'message' => 'Successfully put ' . $data['quantity'] . ' up for sale.',
					'update_areas' => array(
						'crop-yields-' . $yield->getPlotCrop()->getId(),
					),
					'html' => array(
						'crop-yields-' . $yield->getPlotCrop()->getId() => $this->view->partial('crops/yields/_list.phtml', array('pCrop' => $yield->getPlotCrop())),
					)
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

		$this->getHelper()->json->sendJson($response);
	}

	/**
	 * @return mixed
	 */
	public function unListAction()
	{
		$this->_initAjax();
		$response = array();

		if ($yieldId = $this->getRequest()->getParam('pid')) {
			try {
				$yieldPurchasable = Elm::getModel('yield/purchasable')->load($yieldId);
				$yieldPurchasable->unList();
				$response = array(
					'success' => true,
					'message' => 'Successfully un-listed items for sale on the market.',
					'update_areas' => array(
						'crop-yields-' . $yieldPurchasable->getPlotCrop()->getId(),
					),
					'html' => array(
						'crop-yields-' . $yieldPurchasable->getPlotCrop()->getId() => $this->view->partial('crops/yields/_list.phtml', array('pCrop' => $yieldPurchasable->getPlotCrop())),
					)
				);
			} catch (Exception $e) {
				Elm::logException($e);
				$response = array('error' => true, 'message' => $e->getMessage());
			}

			$this->getHelper()->json->sendJson($response);
		}
	}

	/**
	 * @return mixed
	 */
	public function reListAction()
	{
		$this->_initAjax();
		$response = array();

		if ($yieldId = $this->getRequest()->getParam('pid')) {
			try {
				$yieldPurchasable = Elm::getModel('yield/purchasable')->load($yieldId);
				$yieldPurchasable->listForSale();
				$response = array(
					'success' => true,
					'message' => 'Successfully listed items for sale on the market.',
					'update_areas' => array(
						'crop-yields-' . $yieldPurchasable->getPlotCrop()->getId(),
					),
					'html' => array(
						'crop-yields-' . $yieldPurchasable->getPlotCrop()->getId() => $this->view->partial('crops/yields/_list.phtml', array('pCrop' => $yieldPurchasable->getPlotCrop())),
					)
				);
			} catch (Exception $e) {
				Elm::logException($e);
				$response = array('error' => true, 'message' => $e->getMessage());
			}

			$this->getHelper()->json->sendJson($response);
		}
	}

	/**
	 * @return mixed
	 */
	public function cancelForSaleAction()
	{
		$this->_redirect($this->getRequest()->getHeader('referer'));
		return;


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