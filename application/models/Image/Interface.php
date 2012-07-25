<?php

interface Elm_Model_Image_Interface
{
	/**
	 * 102400*12 = 1228800
	 */
	const MAX_FILE_SIZE = 1228800;

	/**
	 * Returns the images directory path
	 *
	 * @static
	 * @param $plot
	 * @return string
	 */
	public static function getImagePath($plot);

	/**
	 * Returns the image's url
	 *
	 * @static
	 * @param $plot
	 * @return string
	 */
	public static function getImageUrl($plot);

	/**
	 * @static
	 * @abstract
	 * @param $object
	 * @param $params
	 */
	//public static function upload($object, $params);
}