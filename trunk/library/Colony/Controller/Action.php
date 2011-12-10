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
		Bootstrap::getSingleton('session')->setLastUrl = $this->getCurrentUrl();
        return $this;
    }

    public function getCurrentUrl()
	{
		return $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getRequestUri();
	}
}
