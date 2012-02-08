<?php

/**
 * Base front controller
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Colony_Controller_Action extends Zend_Controller_Action
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

    /**
     * Predispatch: shoud set layout area
     *
     * @return Mage_Core_Controller_Front_Action
     */
    public function preDispatch()
    {
        parent::preDispatch();
        return $this;
    }

    /**
     * Postdispatch: should set last visited url
     *
     * @return Mage_Core_Controller_Front_Action
     */
    public function postDispatch()
    {
        parent::postDispatch();
		Bootstrap::getSingleton('user/session')->setLastUrl = $this->getCurrentUrl();
        return $this;
    }

	/**
	 * Initializes the layout for ajax requests
	 */
	protected function _initAjax()
	{
		$this->_helper->layout()->disableLayout();
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
	public function getUrl($string, $params)
	{
		if  (!$this->_urlHelper) {
			$this->_urlHelper = new Elm_View_Helper_Url();
		}
		return $this->_urlHelper->url($string, $params);
	}
}
