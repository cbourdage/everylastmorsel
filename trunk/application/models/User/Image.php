<?php


class Elm_Model_User_Image implements Elm_Model_Image_Interface
{
	const LOG_FILE = 'user-images.log';
	const DESTINATION_DIR = 'http/media/user';

	/**
	 * @static
	 * @param $object
	 * @return string
	 */
	public static function getImagePath($object)
	{
		if ($object->getId() > 9) {
			return DIRECTORY_SEPARATOR . substr($object->getId(), 0, 1) . DIRECTORY_SEPARATOR . substr($object->getId(), 1, 1);
		}
		return DIRECTORY_SEPARATOR . substr($object->getId(), 0, 1);
	}

	/**
	 * @static
	 * @param $object
	 * @return mixed
	 */
	public static function getImageUrl($object)
	{
		return str_replace(DIRECTORY_SEPARATOR, '/', self::getImagePath($object));
	}

	/**
	 * @param $object
	 * @return string
	 */
	public static function getImageDestination($object)
	{
		$destination = Bootstrap::getBaseDir(self::DESTINATION_DIR) . self::getImagePath($object);
		Bootstrap::log($destination);
		if (!is_dir($destination)) {
			mkdir($destination, 0777, true);
		}

		return $destination;
	}
}