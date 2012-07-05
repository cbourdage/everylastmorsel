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
		//Elm::log('prevent d');
		//return;

		if (!$this->_location) {
			$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$long&sensor=false";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$this->_results = Zend_Json::decode(curl_exec($ch), true);

			//$results = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$long&sensor=false");
			//$this->_results = Zend_Json::decode($results);

			/**
			 * @TODO store location data in database
			 */
			Elm::log($url, Zend_Log::INFO, 'google/' . date('Y-m-d') . '-geolocation-requests.log');
			Elm::log($this->_results, Zend_Log::INFO, 'google/' . date('Y-m-d') . '-geolocation-requests.log');

			$this->_location = $this->_results['results'][0];

			// Log data
			Elm::log(
				sprintf("%s,%s:%s,%s,%s,%s", $lat, $long, $this->getCity(), $this->getState(), $this->getZip(), $this->getCounty()),
				Zend_Log::INFO,
				'geolocation-data.log'
			);
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
		return $this->_results['results'][0]['address_components'][2]['long_name'];
	}

	/**
	 * Returns county information
	 *
	 * @return string
	 */
	public function getCounty()
	{
		return $this->_results['results'][0]['address_components'][4]['long_name'];
	}

	/**
	 * Returns the state
	 *
	 * @return string
	 */
	public function getState()
	{
		return $this->_results['results'][0]['address_components'][5]['long_name'];
	}

	/**
	 * Returns the zipcode
	 *
	 * @return string
	 */
	public function getZip()
	{
		return $this->_results['results'][0]['address_components'][7]['long_name'];
	}
}