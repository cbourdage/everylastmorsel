<?php

class Elm_View_Helper_Plot extends Zend_View_Helper_Abstract
{
	private $_session = null;

	/**
	 * @return Elm_View_Helper_Plot
	 */
	public function Plot()
	{
		$this->_session = Bootstrap::getSingleton('user/session');
		return $this;
	}

	public function getFeed($plot, $count=10)
	{
		$feed = $plot->getFeed($count);
		return $feed;
	}
}