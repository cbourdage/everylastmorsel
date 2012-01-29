<?php

class Elm_View_Helper_Map extends Zend_View_Helper_Abstract
{
	private $_plots = null;

	public function Map()
	{
		return $this;
	}

	public function getPlotJson()
	{
		$this->_plots = Bootstrap::getModel('plot')->getAllPlots();
		return Zend_Json::encode($this->_plots->toArray());
	}
}