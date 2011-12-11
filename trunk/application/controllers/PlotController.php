<?php

class Elm_PlotController extends Colony_Controller_Action
{
	protected function _getSession()
	{
		return Bootstrap::getSingleton('user/session');
	}

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
		$form = new Elm_Model_Plot_Form_Create();
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
}