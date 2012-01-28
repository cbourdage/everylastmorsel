<?php

class Elm_User_AbstractController extends Colony_Controller_Action
{
	protected $_urlHelper;

	protected $_session;

	public function init()
	{
		$action = $this->getRequest()->getActionName();
        $pattern = '/^(create|login)/i';
        if (!preg_match($pattern, $action)) {
         	$layout = $this->_helper->layout();
			$layout->setLayout('two-column');
        }
	}

	protected function _getSession()
	{
		if (!$this->_session) {
			$this->_session = Bootstrap::getSingleton('user/session');
		}
		return $this->_session;
	}

	public function getUrl($string, $params)
	{
		if  (!$this->_urlHelper) {
			$this->_urlHelper = new Elm_View_Helper_Url();
		}
		return $this->_urlHelper->url($string, $params);
	}
}