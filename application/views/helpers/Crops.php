<?php
/**
 * Helper for making easy links and getting urls that depend on the routes and router
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Elm_View_Helper_Crops extends Zend_View_Helper_Url
{
    public function Crops()
	{
		return $this;
	}

	public function getDefaultCrops()
	{
		$tempJson = array();
		$defaults = Elm::getModel('crop')->getDefaultVariety();
		foreach ($defaults as $crop) {
			$tempJson[] = array('id' => $crop->getId(), 'value' => $crop->getVariety(), 'label' => $crop->getVariety());
		}
		return Zend_Json::encode($tempJson);
	}
}