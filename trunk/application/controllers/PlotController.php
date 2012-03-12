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
        $pattern = '/^(image|image|involve|watch)/i';
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

			if ($this->_plot->getIsStartup()) {
				$this->_forward('startup');
			}
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
				'location' => $this->_helper->url('/user/login')
			);
        } else {
			$form = new Elm_Model_Form_Plot_Create();
			if ($this->getRequest()->isPost()) {
				$post = $this->getRequest()->getPost();
				if ($form->isValid($post)) {
					try {
						$plot = Bootstrap::getModel('plot');
						$plot->setData($post);
						if ($this->getRequest()->getParam('type') == 'shouldBeA') {
							$plot->setIsStartup(true);
						}
						$plot->save();

						$plot->createNewPlotStatus()->sendNewPlotEmail();
						$session->addSuccess("This location looks great!");

						if (isset($post['role'])) {
							$plot->associateUser($post['user_id'], $post['role'], true);
						}

						$response = array(
							'success' => true,
							'error' => false,
							'location' => '/p/' . $plot->getId()
						);
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

	/**
	 * Saving properties on the profile page
	 */
	public function saveAction()
	{
		$this->_initAjax();
		$session = $this->_getSession();

		if (!$session->isLoggedIn()) {
			$response = array(
				'success' => false,
				'error' => true,
				'location' => $this->_helper->url('user/login')
			);
        } else {
			if ($this->getRequest()->isPost()) {
				$post = $this->getRequest()->getPost();
				$plot = Bootstrap::getModel('plot')->load($post['plot_id']);
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
		}

		$this->_helper->json->sendJson($response);
	}

	/**
	 * Involves a user to a plot
	 */
	public function involveMeAction()
	{
		if ($this->getRequest()->isPost()) {
			$post = $this->getRequest()->getPost();
			if (isset($post['role'])) {
				$plot = Bootstrap::getModel('plot')->load($post['plot_id']);
				$plot->associateUser($post['user_id'], $post['role'], false);
			}
			$this->_redirect('/p/' . $post['plot_id']);
		} else {
			$this->_redirect('/');
		}
	}

	public function approveUserAction()
	{
		$request = $this->getRequest();
		if ($request->getParam('user_id') && $request->getParam('plot_id')) {
			$plot = Bootstrap::getModel('plot')->load($request->getParam('plot_id'));
			$plot->approveUser($request->getParam('user_id'), $request->getParam('role'));
			$this->_redirect('/p/' . $request->getParam('plot_id'));
		} else {
			$this->_redirect('/');
		}
	}

	/**
	 * Sets a user to 'watching' a plot
	 */
	public function watchThisAction()
	{
		if ($this->getRequest()->isPost()) {
			$post = $this->getRequest()->getPost();
			$plot = Bootstrap::getModel('plot')->load($post['plot_id']);
			$plot->associateUser($post['user_id'], Elm_Model_Resource_Plot::ROLE_WATCHER, true);
			$this->_redirect('/p/' . $post['plot_id']);
		} else {
			$this->_redirect('/');
		}
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

		$this->_redirect('/p/' . $this->getRequest()->getParam('p'));
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