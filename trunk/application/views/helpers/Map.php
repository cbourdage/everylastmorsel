<?php

class Elm_View_Helper_Map extends Zend_View_Helper_Abstract
{
	const STATIC_MAP_URL = 'http://maps.googleapis.com/maps/api/staticmap?';
	const STATIC_MAP_TYPE = 'maptype=roadmap';
	const STATIC_MAP_FORMAT = 'format=jpeg';
	const STATIC_MAP_SENSOR = 'sensor=false';
	const STATIC_MAP_ZOOM = 'zoom=14';
	const STATIC_MAP_SIZE = 'size=200x200';

	private $_plots = null;

	public function Map()
	{
		return $this;
	}

	public function getPlotJson()
	{
		$this->_plots = Elm::getModel('plot')->getAllPlots();
		$tempJson = array();
		foreach ($this->_plots as $key => $plot) {
			$html = '<div><h3><a href="' . $plot->getUrl() . '" title="' . $plot->getName() . '">' . $plot->getName() . '</a></h3>'
				. '<p>' . ($plot->getAbout() ? $plot->getAbout() : '') . '</p>';
			$html .= ($plot->getIsStartup()) ? '<p><strong>Help me startup</strong>' : '';
			$html .= '</div>';
			$plot->setData('infoWindowHtml', $html);
			array_push($tempJson, $plot->getData());
		}
		return Zend_Json::encode($tempJson);
	}

	/**
	 * Returns the maps image url
	 *
	 * @param Elm_Model_Plot $plot
	 * @param array $options // Deprecated
	 * @return string
	 */
	public function getMapImage($plot, $options = array())
	{
		return $this->_getGoogleMapImage($plot, $options);

		// attempt to circumvent google to save the image...
		if ($this->_hasImageExpired($plot)) {
			try {
				// Curl request
				//throw new Exception('blah');
				Elm::log('saving');
				$filename = md5($plot->getName()) . '.jpg';
				$imageFile = Elm_Model_Plot_Image::getImageDestination($plot) . DIRECTORY_SEPARATOR . $filename;
				Elm::log($imageFile);
				/*$client = new Zend_Http_Client(self::STATIC_MAP_URL, array(
					'adapter'   => 'Zend_Http_Client_Adapter_Curl',
					'curloptions' => array(CURLOPT_FOLLOWLOCATION => true),
				));
				Elm::log($this->_getGoogleMapImage($plot, false));
				$client->setParameterGet($this->_getGoogleMapImage($plot, $options, false));

				// Response
				$response = $client->request('GET');
				Elm::log($response);
				$client->setStream($imageFile)->request('GET');
				*/

				$contents = file_get_contents($this->_getGoogleMapImage($plot, $options));
				$fh = fopen($imageFile, 'w+');
				fputs($fh, $contents);
				fclose($fh);

				$plot->setImageRetrievedAt(new Zend_Db_Expr('now()'));
				$plot->setImage(Elm_Model_Plot_Image::getImageUrl($plot) . '/' . $filename);
				$plot->save();
			} catch (Exception $e) {
				Elm::logException($e);
				return $this->_getGoogleMapImage($plot, $options);
			}
		}

		return Elm::getBaseUrl('media/plot') . $plot->getImage();
	}

	/**
	 * Builds google map url image request. Returns string of url or all query
	 * string parameters based on flag
	 *
	 * * http://maps.googleapis.com/maps/api/staticmap?center=Brooklyn+Bridge,New+York,NY&zoom=14&size=512x512&maptype=roadmap&markers=color:blue%7Clabel:S%7C40.702147,-74.015794&markers=color:green%7Clabel:G%7C40.711614,-74.012318&markers=color:red%7Ccolor:red%7Clabel:C%7C40.718217,-73.998284&sensor=false
	 * http://maps.googleapis.com/maps/api/staticmap?center=Lombard,IL
	 *		 &zoom=15
	 *		 &size=250x250
	 *		 &format=jpeg
	 *		 &maptype=roadmap
	 *		 &sensor=false
	 *		 &markers=color:red%7Clabel:S%7C40.702147,-74.015794
	 *
	 * http://maps.googleapis.com/maps/api/staticmap?zoom=14&size=250x250&format=jpeg&maptype=roadmap&sensor=false&center=41.87983472,-88.03042598&marker=color:red|label:S|41.87983472,-88.03042598
	 *
	 * @param $plot
	 * @param $options
	 * @param bool $flag
	 * @return array|string
	 */
	protected function _getGoogleMapImage($plot, $options, $flag = true)
	{
		$size = self::STATIC_MAP_SIZE;
		$zoom = self::STATIC_MAP_ZOOM;
		if (count($options) > 0) {
			if (isset($options['size'])) {
				$size = sprintf('size=%dx%d', $options['size']['height'], $options['size']['width']);
			}
			if (isset($options['zoom'])) {
				$size = sprintf('zoom=%d', $options['zoom']);
			}
		}

		if ($flag === true) {
			$url = self::STATIC_MAP_URL . implode('&', array(
				$zoom,
				$size,
				self::STATIC_MAP_FORMAT,
				self::STATIC_MAP_TYPE,
				self::STATIC_MAP_SENSOR,
				sprintf('center=%s,%s', $plot->getLatitude(), $plot->getLongitude()),
				sprintf('markers=color:red|label:S|%s,%s', $plot->getLatitude(), $plot->getLongitude())
			));
			Elm::logGoogleRequest($url);
			return $url;
		} else {
			return array(
				$zoom,
				$size,
				self::STATIC_MAP_FORMAT,
				self::STATIC_MAP_TYPE,
				self::STATIC_MAP_SENSOR,
				sprintf('center=%s,%s', $plot->getLatitude(), $plot->getLongitude()),
				sprintf('markers=color:red|label:S|%s,%s', $plot->getLatitude(), $plot->getLongitude())
			);
		}
	}

	/**
	 * Checks plot main image expiration date
	 *
	 * @param $plot
	 * @return bool
	 */
	protected function _hasImageExpired($plot)
	{
		Elm::log(__METHOD__);
		Elm::log($plot->getImage());
		Elm::log($plot->getImageRetrievedAt());
		die('dead');

		if ($plot->getImage() && Zend_Date::isDate($plot->getImageRetrievedAt())) {
			$date = new Zend_Date($plot->getImageRetrievedAt());
			Elm::log($date);
			Elm::log($date->add('1', Zend_Date::MONTH));
			Elm::log(Zend_Date::now());
			die('deda checking');
			//return $date->add('1', Zend_Date::MONTH)
		}
		return true;
	}
}