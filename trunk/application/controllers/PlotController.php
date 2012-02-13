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
	 * Registration and registration post action
	 *
	 * @TODO create error messages
	 * @TODO Error Validation
	 * 
	 * @return void
	 */
	public function createAction()
	{
		$session = $this->_getSession();
		$form = new Elm_Model_Form_Plot_Create();
		if ($this->getRequest()->isPost()) {
			$errors = array();
			$post = $this->getRequest()->getPost();
			$plot = Bootstrap::getModel('plot');
			if ($form->isValid($post)) {
				$plot->setData($post)->save();

				// setup session, send email, add messages, move on
				$plot->sendNewPlotEmail();
				$session->addSuccess("This location looks great!");
				$this->_redirect('/p/' . $plot->getId());
				return;
			}
			else {
				/*if (is_array($errors)) {
					foreach ($errors as $errorMessage) {
						$session->addError($errorMessage);
					}
				} else {
					$session->addError($this->__('Invalid user data'));
				}*/
			}
		}

		$this->view->form = $form;
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
		if (!$this->_getSession()->isLoggedIn()) {
			$this->_redirect('/user/login');
			return;
		}

		if (!$id = $this->getRequest()->getParam('p')) {
			$this->_forward('no-route');
			return;
		}

		if (!$plot = Bootstrap::getModel('plot')->load($id)) {
			$this->_forward('no-route');
			return;
		}

		$form = new Elm_Model_Form_Plot_Images();
		$form->setAction('/plot/image-upload');
		$this->view->form = $form;
		$this->view->plot = $plot;
		$this->_initLayout();
	}

	/**
	 * Plot images upload action
	 */
	public function imageUploadAction()
	{
		if (!$this->_getSession()->isLoggedIn()) {
			$this->_redirect('/user/login');
			return;
		}

		if (!$id = $this->getRequest()->getParam('p')) {
			$this->_forward('no-route');
			return;
		}

		if (!$this->getRequest()->isPost()) {
			$this->_redirect('/plot/images/p/' . $id);
			return;
		}

		try {
			$form = new Elm_Model_Form_Plot_Images();
			$post = $this->getRequest()->getParams();	// pass in directly
			$plot = Bootstrap::getModel('plot')->load($id);

			Bootstrap::log($post);
			if ($plot->addImages($post)) {
				$this->_getSession()->addSuccess('Successfully uploaded images');
			} else {
				$this->_getSession()->addError('Oops! Check the form fields are filled out accurately and try again.');
			}
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}

		$this->_redirect('/plot/images/p/' . $post['p']);
	}

	/**
	 * Removes image links from profiles based on an array of ids
	 */
	public function imageRemoveAction()
	{

	}
}