<?php

require_once 'controllers/Plot/AbstractController.php';

class Elm_PlotController extends Elm_Plot_AbstractController
{
	/**
	 * Pre Dispatch check for invalid session
	 */
	public function preDispatch()
	{
        parent::preDispatch();

        $action = $this->getRequest()->getActionName();
        $pattern = '/^(image|view)/i';
        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->_redirect('/user/login');
            }
        }
	}

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
	 *
	 * @TODO: view individual post action
	 * @see http://twitter.com/#!/cbourdage/status/159422562189840385
	 */
	public function viewAction()
	{
		$id = $this->getRequest()->getParam('id');
		if ($plot = Bootstrap::getModel('plot')->load($id)) {
			Zend_Registry::set('current_plot', $plot);
			$this->view->plot = $plot;
			$this->view->headTitle()->prepend($plot->getName());
		} else {
			$this->_forward('no-route');
		}
		$this->_initLayout();
	}

	/**
	 * Create new status message action
	 */
	public function createAction()
	{
		$response = array();
		$session = $this->_getSession();

		//if ($this->getRequest()->getParam('isAjax', true)) {
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
		//}

        if (!$session->isLoggedIn()) {
			$response = array(
				'success' => false,
				'error' => true,
				'location' => $this->_helper->url('user/login')
			);
        } else {
			$form = new Elm_Model_Form_Plot_Create();
			if ($this->getRequest()->isPost()) {
				$post = $this->getRequest()->getPost();
				Bootstrap::log($post);
				$plot = Bootstrap::getModel('plot');
				if ($form->isValid($post)) {
					try {
						$plot->setData($post)->save();
						$plot->sendNewPlotEmail();
						$session->addSuccess("This location looks great!");

						if (isset($post['role'])) {
							$plot->associateUser($post['user_id'], $post['role']);
						}

						if (Bootstrap::getSingleton('user/session')->plot['type'] != 'isA') {
							$response = array(
								'success' => true,
								'error' => false,
								'location' => $this->view->url('plot/startup')
							);
						} else {
							$response = array(
								'success' => true,
								'error' => false,
								'location' => '/p/' . $plot->getId()
							);
						}
					} catch (Exception $e) {
						$response = array(
							'success' => false,
							'error' => true,
							'message' => $e->getMessage()
						);
					}
				} else {
					$response = array(
						'success' => false,
						'error' => true,
						'message' =>'Oops! Check required fields and try again.'
					);
				}
			}
		}

		$this->_helper->json->sendJson($response);
	}
}