<?php

require_once 'controllers/Profile/AbstractController.php';

class Elm_SaleController extends Elm_Profile_AbstractController
{
	/**
	 * initializes layout for ajax requests
	 * @TODO this should be moved into extended object as it's used globally...
	 */
	protected function _initAjax()
	{
		$this->getHelper()->layout()->disableLayout();
	}

	public function preDispatch()
	{
		if ($this->getRequest()->isXmlHttpRequest()) {
			if (!Elm::getSingleton('user/session')->isLoggedIn()) {
				$this->getHelper()->json->sendJson(array(
					'success' => false,
					'error' => true,
					//'location' => $this->view->url('profile/login')
					'message' => 'You must be logged in to purchase'
				));
			}
		} else {
			parent::preDispatch();
		}

		return $this;
	}

	public function purchaseAction()
	{
		$this->_initAjax();

		if (!$this->getRequest()->isXmlHttpRequest()) {
			$this->_redirect('community/marketplace');
			return $this;
		}

		if (!$this->getRequest()->getParam('purchasable_id')) {
			$this->getHelper()->json->sendJson(array(
				'success' => false,
				'error' => true,
				'message' => 'Invalid request'
			));
			return $this;
		}

		$response = array();
		$data = $this->getRequest()->getParams();
		try {
			$transaction = Elm::getModel('yield/transaction')->setData($data);
			if ($transaction->validate()) {
				$transaction->purchase($data);
				$response = array(
					'success' => true,
					'message' => sprintf('Your request to purchase %s has been made.', $transaction->getPurchasableObject()->getCrop()->getName())
				);
			}
		} catch(Exception $e) {
			$response = array(
				'success' => false,
				'error' => true,
				'message' => $e->getMessage()
			);
		}
		$this->getHelper()->json->sendJson($response);
	}
}