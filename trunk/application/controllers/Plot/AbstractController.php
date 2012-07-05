<?php

require_once 'controllers/AbstractController.php';

class Elm_Plot_AbstractController extends Elm_AbstractController
{
	protected $_plot = null;

	/**
	 * Pre Dispatch check for invalid session
	 */
	public function preDispatch()
	{
        parent::preDispatch();

        $action = $this->getRequest()->getActionName();
        $pattern = '/^(image|involve|watch|pendingApproval|create)/i';
        if (preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->_redirect('/profile/login');
            }
        }
	}

	public function _init()
	{
		$this->_plot = Elm::getModel('plot')->load($this->getRequest()->getParam('p'));
		Zend_Registry::set('current_plot', $this->_plot);

		$this->view->plot = $this->_plot;
		$this->view->canContact = $this->_plot->getVisibility() == Elm_Model_Form_User_Settings::VISIBILITY_PUBLIC ? true : false;
		$this->view->headTitle()->prepend($this->_plot->getName());

		return $this;
	}

	protected function _isValid()
	{
		if (!$id = $this->getRequest()->getParam('p')) {
			return false;
		}

		if (!$plot = Elm::getModel('plot')->load($id)) {
			return false;
		}

		return true;
	}

	/**
	 * Initializes the User layout objects
	 */
	protected function _initLayout()
	{
		$action = $this->getRequest()->getActionName();
        $pattern = '/^(create|login)/i';
        if (!preg_match($pattern, $action)) {
         	$layout = $this->_helper->layout();
			$layout->setLayout('profile-layout');
        }

		$this->view->placeholder('contact-modal')->set($this->view->render('communication/_modal.phtml'));
		$this->view->placeholder('sidebar')->set($this->view->render('plot/_sidebar.phtml'));
	}
}