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

		// @TODO figure out redirects for login and registration pages - all pages for that matter.
        $action = $this->getRequest()->getActionName();
        $pattern = '/^(images|image)/i';
        if (preg_match($pattern, $action)) {
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
	 */
	public function viewAction()
	{
		if (!$this->_isValid()) {
			$this->_forward('no-route');
		} else {
			$this->_initCurrentPlot();
			$this->view->plot = $this->_plot;
			$this->view->headTitle()->prepend($this->_plot->getName());
		}
		$this->_initLayout();
	}

	/**
	 * Registration and registration post action ajax
	 */
	public function createAjaxAction()
	{
		$response = array();
		$session = $this->_getSession();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

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

	/**
	 * Plot startup action
	 */
	public function startupAction()
	{
	}

	public function saveAction()
	{
		$this->_initAjax();
		if ($this->getRequest()->isPost()) {
			$post = $this->getRequest()->getPost();

			Bootstrap::log(__METHOD__);
			Bootstrap::log($post);

			$plot = Bootstrap::getModel('plot')->load($this->getRequest()->getParam('plot_id'));
			$plot->setData($post['plot_update'], $post[$post['plot_update']]);
			$plot->save();
			$response = array(
				'success' => true,
				'error' => false,
				'message' =>'Ah, success!',
				'value' => $plot->getData($post['plot_update'])
			);
		} else {
			$response = array(
				'success' => false,
				'error' => true,
				'message' =>'Oops! Check required fields and try again.'
			);
		}

		$this->_helper->json->sendJson($response);
	}

	/**
	 * Plot images default
	 */
	public function imagesAction()
	{
		if (!$this->_isValid()) {
			$this->_forward('no-route');
			return;
		}

		$this->_initCurrentPlot();

		$form = new Elm_Model_Form_Plot_Images();
		$form->setAction('/plot/image-upload/p/' . $this->_plot->getId());
		$this->view->form = $form;
		$this->view->plot = $this->_plot;
		$this->_initLayout();
	}

	/**
	 * Plot images upload action
	 */
	public function imageUploadAction()
	{
		if (!$this->_isValid()) {
			$this->_forward('no-route');
			return;
		}

		if (!$this->getRequest()->isPost()) {
			$this->_redirect('/plot/images/p/' . $this->getRequest()->getParam('p'));
			return;
		}

		$this->_initCurrentPlot();
		$this->_plot->addImages($this->getRequest()->getParams());

		$this->_redirect('/plot/images/p/' . $this->getRequest()->getParam('p'));
	}

	/**
	 * Removes image links from profiles based on an array of ids
	 */
	public function imageRemoveAction()
	{
		if (!$this->_isValid()) {
			$this->_forward('no-route');
			return;
		}

		if (!$this->getRequest()->getParam('images', null)) {
			$this->_redirect('/plot/images/p/' . $this->getRequest()->getParam('p'));
			return;
		}

		$this->_initCurrentPlot();
		$this->_plot->removeImages($this->getRequest()->getParam('images'));

		$this->_redirect('/plot/images/p/' . $this->getRequest()->getParam('p'));
	}
}