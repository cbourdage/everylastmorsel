<?php

/**
 * Helper for making easy links and getting urls that depend on the routes and router
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Elm_View_Helper_Data extends Zend_View_Helper_Url
{
    public function Data()
	{
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDomain()
	{
		return Zend_Controller_Front::getInstance()->getRequest()->getHttpHost() . '/';
	}

	/**
	 * @return string
	 */
	public function getEnvironment()
	{
		return APPLICATION_ENV;
	}

	/**
	 * @return mixed
	 */
	public function getMapsApi()
	{
		return Elm::getAppConfig('app/mapsapi');
	}

	public function formatDate($date, $format = null)
	{
		if (!Zend_Date::isDate($date, 'YYYY-MM-dd')) {
			return '';
		}

		$zd = new Zend_Date(strtotime($date));
		if ($format === null) {
			$format = 'MMMM d, YYYY';
		}

		return $zd->toString($format);
	}

	public function formatPrice($price)
	{
		$price = new Zend_Currency(array('precision' => 2, 'value' => $price));
		return $price;
	}
}
