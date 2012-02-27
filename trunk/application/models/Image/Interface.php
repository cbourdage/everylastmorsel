<?php

interface Elm_Model_Image_Interface
{

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
}