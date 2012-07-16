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
		$this->_helper->layout()->setLayout('home-page');
    }

	/**
	 *
	 */
	public function plotPointAction()
	{
		$this->_initAjax();
		$this->_helper->viewRenderer->setNoRender(true);

		// Set session data
		Elm::getSingleton('user/session')->plot = array(
			'latitude' => $this->getRequest()->getParam('lat'),
			'longitude' => $this->getRequest()->getParam('long'),
			'type' => $this->getRequest()->getParam('type'),
		);

		if (Elm::getSingleton('user/session')->isLoggedIn()) {
			$response = array(
				'success' => true,
				'error' => false,
				'location' => $this->view->url('plot/create')
			);
			$this->_helper->json->sendJson($response);
		} else {
			$html = new Zend_View();
			$html->setScriptPath(APPLICATION_PATH . '/views/scripts/profile/');
			$response = array(
				'success' => false,
				'error' => true,
				'title' => 'Login to your account!',
				'html' => $html->render('_login.phtml')
			);
			$this->_helper->json->sendJson($response);
		}

		//$this->getResponse()->sendResponse();
	}

	/**
	 *
	 */
	public function initLocationAction()
	{
		$this->_initAjax();
		$response = array();

		if ($location = Elm::getSingleton('session')->location) {
			$response = array(
				'success' => true,
				'error' => false,
				'city' => $location->getCity(),
				'state' => $location->getState(),
				'zip' => $location->getZip()
			);
		} else {
			$request = $this->getRequest();
			if ($request->getParam('lat', false) && $request->getParam('long', false)) {
				$geo = new Elm_Model_Geolocation($request->getParam('lat'), $request->getParam('long'));

				// set into session to reduce the # of calls
				Elm::getSingleton('session')->location = $geo;
				$response = array(
					'success' => true,
					'error' => false,
					'city' => $geo->getCity(),
					'state' => $geo->getState(),
					'zip' => $geo->getZip()
				);
			}
		}

		$this->_helper->json->sendJson($response);
	}

	/**
	 * About Us Action
	 *
	 * @return void
	 */
	public function aboutAction()
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
	 *
	 */
	public function comingSoonAction()
	{
		if ($this->getRequest()->isPost()) {
			/**
			 * @TODO create new model and resource for email signup
			 */
			//if (1 == 2) {
				$model = Elm::getModel('earlybirds');
				if (!$model->isTaken($this->getRequest()->getParam('email'))) {
					$model->setEmail($this->getRequest()->getParam('email'))
						->setRegion($this->getRequest()->getParam('region'))
						->setIpAddress(new Zend_Db_Expr('INET_ATON("' . $_SERVER['REMOTE_ADDR'] . '")'));
					$model->save();
					$model->sendEmailNotification();
				}

				Elm::getSingleton('user/session')->addSuccess('Thank you! We will notify you of upcoming news.');
			//}
		}

		$this->_helper->layout()->setLayout('coming-soon');
		$this->view->headLink()->offsetUnset(4);
		$this->view->headLink()->offsetUnset(3);
		$this->view->headLink()->offsetUnset(2);
		$this->view->headLink()->appendStylesheet('/file-bin/css/coming-soon.css');
	}
}

