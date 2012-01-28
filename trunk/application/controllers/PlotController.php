<?php

require_once 'controllers/Plot/AbstractController.php';

class Elm_PlotController extends Elm_Plot_AbstractController
{
	public function indexAction()
	{
		$this->_forward('view');
	}

	public function viewAction()
	{
		$id = $this->getRequest()->getParam('id');
		if ($plot = Bootstrap::getModel('plot')->load($id)) {
			$this->view->plot = $plot;
			$this->view->message = 'Plot account: ';
		}
		else {
			// forward to invalid
			$this->view->message = 'Invalid plot account...';
			//$this->_forward('noRoute');
		}
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

	public function startupAction()
	{
	}
}