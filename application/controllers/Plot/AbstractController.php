<?php

require_once 'controllers/AbstractController.php';

class Elm_Plot_AbstractController extends Elm_AbstractController
{
    /**
     * @var Elm_Model_Plot
     */
    protected $_plot;

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

    /**
     * Checks if the request is valid
     *
     * @return bool
     */
    protected function _isValid()
	{
		if (!$id = $this->getRequest()->getParam('p')) {
			return false;
		}

		$plot = Elm::getModel('plot')->load($id);
		if (!$plot->getId()) {
			return false;
		}

		return true;
	}

    /**
     * Initializes plot view data
     *
     * @return $this
     */
    public function _init()
	{
		$this->_plot = Elm::getSingleton('plot')->load($this->getRequest()->getParam('p'));
		Zend_Registry::set('current_plot', $this->_plot);
		Zend_Registry::set('current_user', $this->_plot->getOwner());

		$this->view->plot = $this->_plot;
        $this->view->canContact = ($this->_plot->getVisibility() == Elm_Model_Form_Plot_Settings::VISIBILITY_PUBLIC);
		$this->view->headTitle()->prepend($this->_plot->getName());
		return $this;
	}

	/**
	 * Initializes the User layout objects
     *
     * @return $this
	 */
	protected function _initLayout()
	{
		$action = $this->getRequest()->getActionName();
        /*$pattern = '/^(create|login)/i';
        if (!preg_match($pattern, $action)) {*/
            $layout = $this->getHelper()->layout();
            $layout->setLayout('profile-layout');
        //}

		//$this->view->placeholder('contact-modal')->set($this->view->render('communication/contact/owner.phtml'));
		$this->view->placeholder('sidebar')->set($this->view->render('plot/_sidebar.phtml'));
        return $this;
	}

    /**
     * Checks current user has permissions ot access this action
     *
     * @return bool
     */
    protected function _canAccess()
    {
        return $this->_plot && $this->_plot->isOwner($this->_getSession()->getUser());
    }
}