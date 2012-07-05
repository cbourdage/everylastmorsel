<?php

/**
 * Base front controller
 */
class Elm_AbstractController extends Zend_Controller_Action
{
    /**
     * Currently used area
     *
     * @var string
     */
    protected $_currentArea = 'frontend';

    /**
     * Namespace for session.
     *
     * @var string
     */
    protected $_sessionNamespace = 'frontend';

	/**
	 * Url helper object
	 *
	 * @var Elm_View_Helper_Url
	 */
	protected $_urlHelper;

	protected $_session;

	protected $_isAjax = false;

    /**
     * Predispatch: should set layout area
     *
     * @return Colony_Controller_Action
     */
    public function preDispatch()
    {
        parent::preDispatch();

		if (!preg_match('/^(login|create|coming-soon|error)/i', $this->getRequest()->getActionName())) {
			if (!Elm::getSingleton('user/session')->isLoggedIn()) {
				//$this->_redirect('/coming-soon/');
			}
		}

		$this->view->bodyClass = strtolower(implode('-', array(
			$this->getRequest()->getModuleName(),
			$this->getRequest()->getControllerName(),
		)));
		$this->view->bodyClass .= ' ' . strtolower(implode('-', array(
			$this->getRequest()->getModuleName(),
			$this->getRequest()->getControllerName(),
			$this->getRequest()->getActionName()
		)));

        return $this;
    }

    /**
     * Sets last visited url
     *
     * @return Zend_Controller_Action
     */
    public function postDispatch()
    {
        parent::postDispatch();
		$session = Elm::getSingleton('user/session');

		if (!$this->_isAjax && !preg_match('/(favicon)/i', $this->getCurrentUrl())) {
			$session->lastUrl = $this->getCurrentUrl();
			$session->beforeAuthUrl = $this->getCurrentUrl();
		}
		// @TODO start using $helper->escape();
		$this->view->setEscape('stripslashes');
        return $this;
    }

	/**
	 * Returns users session object
	 *
	 * @return Elm_Model_User_Session
	 */
	protected function _getSession()
	{
		if (!$this->_session) {
			$this->_session = Elm::getSingleton('user/session');
		}
		return $this->_session;
	}

	/**
	 * Initializes the layout for ajax requests
	 */
	protected function _initAjax()
	{
		$this->_helper->layout()->disableLayout();
		$this->_isAjax = true;
	}

	/**
	 * @return string
	 */
    public function getCurrentUrl()
	{
		return $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getRequestUri();
	}

	/**
	 * @param $string
	 * @param $params
	 * @return string
	 */
	public function getUrl($string, $params = array())
	{
		if  (!$this->_urlHelper) {
			$this->_urlHelper = new Elm_View_Helper_Url();
		}
		return $this->_urlHelper->url($string, $params);
	}
}
