<?php

require_once 'controllers/Plot/AbstractController.php';

class Elm_PlotController extends Elm_Plot_AbstractController
{
	/**
	 * Default 404
	 */
	public function noRouteAction()
	{
	}

	public function createAction()
	{
		// Set session data
		$session = $this->_getSession();
		if (!$session->plot) {
			$this->_redirect('/');
			return;
		}

		$this->_initLayout();

		$form = new Elm_Model_Form_Plot_Create();
		$form->setAction('/plot/create-post');

		// Set data if stored in session b/c of an error
		if ($location = Elm::getSingleton('session')->location) {
			$form->setDefaults(array(
				'city' => $location->getCity(),
				'state' => $location->getState(),
				'zipcode' => $location->getZip()
			));
		}

		if ($session->formData) {
			$form->setDefaults($session->formData);
			$session->formData = null;
		}

		$this->view->headTitle()->prepend('Plot Location');
		$this->view->form = $form;
	}

	public function createPostAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->_redirect('/plot/create');
			return;
		}

		$session = $this->_getSession();

		$form = new Elm_Model_Form_Plot_Create();
		$post = $this->getRequest()->getParams();

		if ($form->isValid($post)) {
			try {
				$plot = Elm::getModel('plot');
				$plot->setData($post);
				$plot->setIsStartup(false);
				$plot->save();

				if (isset($post['role'])) {
					$plot->associateUser($post['user_id'], $post['role'], true);
				}
				$user = $session->getUser();
				$user->addData($post)->save();
				$plot->createNewPlotStatus()->sendNewPlotEmail();
				$session->addSuccess("This location looks great!");
				$this->_redirect($plot->getUrl());
				return;
			} catch (Colony_Exception $e) {
				$session->addError($e->getMessage());
			} catch (Exception $e) {
				$session->addError($e->getMessage());
			}
		} else {
			$session->formData = $post;
			$session->addError('Check all fields are filled out correctly.');
		}

		$this->_redirect('/plot/create');
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
	 * Plot images default
	 */
	public function cropsAction()
	{
		if (!$this->_isValid()) {
			$this->_forward('no-route');
			return;
		}

		$this->_initCurrentPlot();

		$this->_initLayout();
		$this->view->plot = $this->_plot;
	}

	/**
	 * Plot images default
	 */
	public function peopleAction()
	{
		if (!$this->_isValid()) {
			$this->_forward('no-route');
			return;
		}

		$this->_initCurrentPlot();

		$this->_initLayout();
		$this->view->plot = $this->_plot;
	}

	/**
	 * Plot images default
	 */
	public function photosAction()
	{
		if (!$this->_isValid()) {
			$this->_forward('no-route');
			return;
		}

		$this->_initCurrentPlot();

		$form = new Elm_Model_Form_Plot_Images();
		$form->setAction('/plot/image-upload/p/' . $this->_plot->getId());

		$this->_initLayout();
		$this->view->plot = $this->_plot;
		$this->view->form = $form;
	}

	/**
	 * Plot images upload action
	 */
	public function photoUploadAction()
	{
		if (!$this->_isValid()) {
			$this->_forward('no-route');
			return;
		}

		if (!$this->getRequest()->isPost()) {
			$this->_redirect('/p/photos/' . $this->getRequest()->getParam('p'));
			return;
		}

		$this->_initCurrentPlot();
		try {
			$this->_plot->addImages($this->getRequest()->getParams());
		} catch (Colony_Exception $e) {
			Elm::logException($e);
			$this->_getSession()->addError('Ah! Sorry, there was an error uploading your photos.');
		}

		$this->_redirect('/p/photos/' . $this->getRequest()->getParam('p'));
	}

	/**
	 * Removes image links from profiles based on an array of ids
	 */
	public function photoRemoveAction()
	{
		if (!$this->_isValid()) {
			$this->_forward('no-route');
			return;
		}

		if (!$this->getRequest()->getParam('photo', null)) {
			$this->_redirect('/p/photos/' . $this->getRequest()->getParam('p'));
			return;
		}

		$this->_initCurrentPlot();
		try {
			$this->_plot->removeImages($this->getRequest()->getParam('photo'));
		} catch (Colony_Exception $e) {
			Elm::logException($e);
			$this->_getSession()->addError('Ah! Sorry, there was an error removing your photos.');
		}

		$this->_redirect('/p/photos/' . $this->getRequest()->getParam('p'));
	}

	/**
	 * Shows list of users pending approval for specified plot
	 */
	public function pendingUsersAction()
	{
		if (!$this->_isValid()) {
			$this->_forward('no-route');
		} else {
			$this->_initCurrentPlot();
			$this->view->headTitle()->prepend('Pending Users');
			$this->view->headTitle()->prepend($this->_plot->getName());

			if (!$this->_plot->isOwner($this->_getSession()->user)) {
				$this->_redirect('/p/' . $this->_plot->getId());
				return;
			}
			$this->view->plot = $this->_plot;
			$this->view->users = $this->_plot->getPendingUsers();
		}
		$this->_initLayout();
	}

	/**
	 * Involves a user to a plot
	 */
	public function involveMeAction()
	{
		if ($this->getRequest()->isPost()) {
			$post = $this->getRequest()->getPost();
			if (isset($post['role'])) {
				$plot = Elm::getModel('plot')->load($post['plot_id']);
				$plot->associateUser($post['user_id'], $post['role'], false);
			}
			$this->_redirect('/p/' . $post['plot_id']);
		} else {
			$this->_redirect('/');
		}
	}

	/**
	 * Approves user action
	 */
	public function approveUserAction()
	{
		$request = $this->getRequest();
		if ($request->getParam('user_id') && $request->getParam('plot_id')) {
			$plot = Elm::getModel('plot')->load($request->getParam('plot_id'));
			$plot->approveUser($request->getParam('user_id'), $request->getParam('role'));
			$this->_redirect('/p/' . $request->getParam('plot_id'));
		} else {
			$this->_redirect('/');
		}
	}

	/**
	 * Approves user action
	 */
	public function denyUserAction()
	{
		$request = $this->getRequest();
		if ($request->getParam('user_id') && $request->getParam('plot_id')) {
			$plot = Elm::getModel('plot')->load($request->getParam('plot_id'));
			$plot->denyUser($request->getParam('user_id'), $request->getParam('role'));
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
			$plot = Elm::getModel('plot')->load($post['plot_id']);
			$plot->associateUser($post['user_id'], Elm_Model_Resource_Plot::ROLE_WATCHER, true);
			$this->_redirect('/p/' . $post['plot_id']);
		} else {
			$this->_redirect('/');
		}
	}







	/**
	 * Registration and registration post action ajax
	 *
	 * @Deprecated
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
						$plot = Elm::getModel('plot');
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
	 * Saving properties on the profile page
	 *
	 * @Deprecated
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
				$plot = Elm::getModel('plot')->load($post['plot_id']);
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
}