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
		$destination = Elm::getBaseDir(self::DESTINATION_DIR) . self::getImagePath($object);
		Elm::log($destination);
		if (!is_dir($destination)) {
			mkdir($destination, 0777, true);
		}

		return $destination;
	}

	/**
	 * Adds a new image to the plot
	 *
	 * @param $user
	 * @param array $params
	 * @return bool
	 */
	public function upload($user, $params)
	{
		$session = Elm::getSingleton('user/session');
		$destination = self::getImageDestination($user);

		$adapter = new Zend_File_Transfer_Adapter_Http();
		$adapter->setDestination($destination)
			->addValidator('Size', false, self::MAX_FILE_SIZE)
			->addValidator('Extension', false, 'jpg,png,gif,jpeg');

		$info = $adapter->getFileInfo('image');
		$info = $info['image'];
		$parts = explode('.', $info['name']);
		$newFilename = md5($info['name']) . '.' . end($parts);

		try {
			// Rename filter
			$adapter->addFilter('Rename', array(
				'target' => $destination . DIRECTORY_SEPARATOR . $newFilename,
				'overwrite' => true
			));

			// Receive and save
			if ($adapter->receive('image')) {
				Elm::log('received image' . self::getImageUrl($user) . '/' . $newFilename);
				// Set image data
				//$this->setData('exif_data', exif_read_data($info['destination'] . DIRECTORY_SEPARATOR . $newFilename));
				$user->setImage(self::getImageUrl($user) . '/' . $newFilename);
				$user->save();
				//$session->addSuccess('Successfully updated your image');
				return array('Successfully updated your image');
			} else {
				return $adapter->getMessages();

				$errors = $adapter->getMessages();
				foreach ($errors as $e) {
					$session->addError($e);
				}
			}
		} catch (Exception $e) {
			Elm::logException($e);
			$session->addException($e);
		}
	}
}