<?php

class Elm_View_Helper_Map extends Zend_View_Helper_Abstract
{
	const STATIC_MAP_URL = 'http://maps.googleapis.com/maps/api/staticmap?';
	const STATIC_MAP_ZOOM = 'zoom=14';
	const STATIC_MAP_TYPE = 'maptype=roadmap';
	const STATIC_MAP_FORMAT = 'format=jpeg';
	const STATIC_MAP_SIZE = 'size=200x200';
	const STATIC_MAP_SENSOR = 'sensor=false';

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

	/**
	 * http://maps.googleapis.com/maps/api/staticmap?center=Brooklyn+Bridge,New+York,NY&zoom=14&size=512x512&maptype=roadmap&markers=color:blue%7Clabel:S%7C40.702147,-74.015794&markers=color:green%7Clabel:G%7C40.711614,-74.012318&markers=color:red%7Ccolor:red%7Clabel:C%7C40.718217,-73.998284&sensor=false
	 * http://maps.googleapis.com/maps/api/staticmap?center=Lombard,IL
	 * 		&zoom=15
	 * 		&size=250x250
	 * 		&format=jpeg
	 * 		&maptype=roadmap
	 * 		&sensor=false
	 * 		&markers=color:red%7Clabel:S%7C40.702147,-74.015794
	 *
	 * http://maps.googleapis.com/maps/api/staticmap?zoom=14&size=250x250&format=jpeg&maptype=roadmap&sensor=false&center=41.87983472,-88.03042598&marker=color:red|label:S|41.87983472,-88.03042598
	 *
	 * @param $plot
	 * @return string
	 */
	public function getStaticImage($plot)
	{
		return self::STATIC_MAP_URL . implode('&', array(
			self::STATIC_MAP_ZOOM,
			self::STATIC_MAP_SIZE,
			self::STATIC_MAP_FORMAT,
			self::STATIC_MAP_TYPE,
			self::STATIC_MAP_SENSOR,
			sprintf('center=%s,%s', $plot->getLongitude(), $plot->getLatitude()),
			sprintf('markers=color:red|label:S|%s,%s', $plot->getLongitude(), $plot->getLatitude()),
		));
	}
}