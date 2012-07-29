<?php

require_once 'controllers/Plot/AbstractController.php';

class Elm_PlotController extends Elm_Plot_AbstractController
{
	public function preDispatch()
	{
		parent::preDispatch();
		if ($plotId = $this->getRequest()->getParam('p')) {
			$plot = Elm::getModel('plot')->load($plotId);
			if ($plot->getId()) {
				$this->_init();
			}
		}

		//if ($this->_plot->is)
	}

	/**
	 * Default 404
	 */
	public function noRouteAction()
	{
	}

	/**
	 * Create action
	 *
	 * @return mixed
	 */
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

	/**
	 * Create post action
	 *
	 * @return mixed
	 */
	public function createPostAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->_redirect('/plot/create');
			return;
		}

		$form = new Elm_Model_Form_Plot_Create();
		$post = $this->getRequest()->getParams();
		$session = $this->_getSession();

		if ($form->isValid($post)) {
			try {
				$plot = Elm::getModel('plot')->createPlot($post, $session->getUser());
				$session->addSuccess("This location looks great!");
				$this->_redirect($plot->getUrl());
				return;
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
	 * Create action
	 *
	 * @return mixed
	 */
	public function editAction()
	{
		$session = $this->_getSession();
		if (!($plotId = $this->getRequest()->getParam('p', null))) {
			$this->_redirect('/plots/');
			return;
		}

		$plot = Elm::getSingleton('plot')->load($plotId);
		if (!$plot->getId()) {
			$this->_redirect('/plots/');
			return;
		}

		$this->_init();
		$this->_initLayout();

		$form = new Elm_Model_Form_Plot_Edit();
		$form->setAction('/p/edit-post/' . $this->_plot->getId());

		if ($session->formData) {
			$form->setDefaults($session->formData);
			$session->formData = null;
		} else {
			$form->setDefaults($this->_plot->getData());

		}

		$this->view->headTitle()->prepend('Edit Plot');
		$this->view->form = $form;
	}

	/**
	 * Create post action
	 *
	 * @return mixed
	 */
	public function editPostAction()
	{
		$session = $this->_getSession();
		if (!($plotId = $this->getRequest()->getParam('p', null))) {
			die('redirecting to plots/');
			$this->_redirect('/plots/');
			return;
		}

		$this->_init();

		if (!$this->getRequest()->isPost()) {
			die('redirecting to plots/ (second one)');
			$this->_redirect('/p/edit/' . $this->_plot->getId());
			return;
		}

		$form = new Elm_Model_Form_Plot_Edit();
		$post = $this->getRequest()->getParams();
		$session = $this->_getSession();

		if ($form->isValid($post)) {
			try {
				Elm::log($this->_plot->getData());
				//$this->_plot->setData($post);
				$this->_plot->addData($post);
				Elm::log($this->_plot->getData());
				//die('about to save');
				$this->_plot->save();
				$session->addSuccess('Successfully saved your changes');
			} catch (Colony_Exception $e) {
				$session->addError($e->getMessage());
			} catch (Exception $e) {
				$session->addError($e->getMessage());
			}
		} else {
			$session->formData = $post;
			$session->addError('Check all fields are filled out correctly.');
		}

		$this->_redirect('/p/edit/' . $this->_plot->getId());
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
			return;
		}

		$this->_init();
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

		$this->_init();
		$this->_initLayout();
		$this->view->headTitle()->append("Crops");
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

		$this->_init();
		$this->_initLayout();
		$this->view->headTitle()->append("People");
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

		$this->_init();
		$this->_initLayout();
		$this->view->headTitle()->append("People");

		$form = new Elm_Model_Form_Plot_Images();
		$form->setAction('/plot/image-upload/p/' . $this->_plot->getId());
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
		if (!$this->_getSession()->isLoggedIn()) {
			$this->_redirect('/profile/login');
			return;
		}
		if (!$this->_isValid()) {
			$this->_forward('no-route');
			return;
		}
		if (!$this->_plot->isOwner($this->_getSession()->user)) {
			$this->_redirect('/p/' . $this->_plot->getId());
			return;
		}

		$this->_initLayout();
		$this->view->headTitle()->prepend('Pending Users');
		$this->view->users = $this->_plot->getPendingUsers();
	}

	/**
	 * Involves a user to a plot
	 */
	public function involveMeAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->_redirect('/');
		}

		$post = $this->getRequest()->getPost();
		if (isset($post['role'])) {
			try {
				$plot = Elm::getModel('plot')->load($post['plot_id']);
				$plot->associateUser($post['user_id'], $post['role'], false);
				$this->_getSession()->addSuccess('Your involvement is pending owners approval. Hang tight!');
			} catch (Colony_Exception $e) {
				Elm::logException($e);
				$this->_getSession()->addError($e);
			}
		}
		// $session->lastUrl;...
		$this->_redirect('/p/people/' . $post['plot_id']);
	}

	/**
	 * Approves user action
	 */
	public function approveUserAction()
	{
		$request = $this->getRequest();
		if ($request->getParam('user_id') && $request->getParam('plot_id')) {
			try {
				$plot = Elm::getModel('plot')->load($request->getParam('plot_id'));
				$plot->approveUser($request->getParam('user_id'), $request->getParam('role'));
				$this->_getSession()->addSuccess('User has been approved.');
			} catch (Colony_Exception $e) {
				Elm::logException($e);
				$this->_getSession()->addError($e);
			}
			// $session->lastUrl;...
			$this->_redirect('/p/people/' . $request->getParam('plot_id'));
		} else {
			$this->_redirect('/');
		}
	}

	/**
	 * Deny user action
	 */
	public function denyUserAction()
	{
		$request = $this->getRequest();
		if ($request->getParam('user_id') && $request->getParam('plot_id')) {
			try {
				$plot = Elm::getModel('plot')->load($request->getParam('plot_id'));
				$plot->denyUser($request->getParam('user_id'), $request->getParam('role'));
				$this->_getSession()->addSuccess('User has been denied.');
			} catch (Colony_Exception $e) {
				Elm::logException($e);
				$this->_getSession()->addError($e);
			}
			// $session->lastUrl;...
			$this->_redirect('/p/people/' . $request->getParam('plot_id'));
		} else {
			$this->_redirect('/');
		}
	}

	/**
	 * Sets a user to 'watching' a plot
	 */
	public function watchThisAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->_redirect('/');
		}

		$post = $this->getRequest()->getPost();
		try {
			$plot = Elm::getModel('plot')->load($post['plot_id']);
			$plot->associateUser($post['user_id'], Elm_Model_Resource_Plot::ROLE_WATCHER, true);
			$this->_getSession()->addSuccess('Thanks for showing interest. You are now watching this plot.');
		} catch (Colony_Exception $e) {
			Elm::logException($e);
			$this->_getSession()->addError($e);
		}

		// $session->lastUrl;...
		$this->_redirect('/p/' . $post['plot_id']);
	}
}