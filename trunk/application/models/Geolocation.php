<?php

class Elm_Model_Geolocation extends Colony_Object
{
	protected $_results = null;

	protected $_location = null;

	public function __construct($lat, $long)
	{
		$this->_init($lat, $long);
	}

	/**
	 * @param $lat
	 * @param $long
	 */
	protected function _init($lat, $long)
	{
		Elm::log('prevent d');
		return;

		if (!$this->_location) {
			$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$long&sensor=false";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$this->_results = Zend_Json::decode(curl_exec($ch), true);

			//$results = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$long&sensor=false");
			//$this->_results = Zend_Json::decode($results);

			Elm::log($url, Zend_Log::INFO, 'geolocation-requests.log');

			$this->_location = $this->_results['results'][0];
			Elm::log($this->_location);
		}
	}

	/**
	 * @return null | array
	 */
	public function getLocation()
	{
		return $this->_location;
	}

	/**
	 * Returns the city
	 *
	 * @return string
	 */
	public function getCity()
	{
		return $this->_location['address_components'][0]['long_name'];
	}

	/**
	 * Returns the state
	 *
	 * @return string
	 */
	public function getState()
	{
		return $this->_location['address_components'][3]['long_name'];
	}

	/**
	 * Returns the zipcode
	 *
	 * @return string
	 */
	public function getZip()
	{
		return $this->_results['results'][3]['address_components'][0]['long_name'];
	}
}