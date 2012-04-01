<?php


class Elm_Model_Plot_Image extends Colony_Model_Abstract implements Elm_Model_Image_Interface
{
	const LOG_FILE = 'plot-images.log';
	const DESTINATION_DIR = 'http/media/plot';

	public function _construct()
    {
        $this->_init('plot_image');
    }

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
		if (!is_dir($destination)) {
			mkdir($destination, 0777, true);
		}
		return $destination;
	}

	/**
	 * Adds a new image to the plot
	 *
	 * @param $plot
	 * @param array $params
	 * @return bool
	 */
	public function upload($plot, $params)
	{
		$session = Elm::getSingleton('user/session');
		$destination = self::getImageDestination($plot);

		$adapter = new Zend_File_Transfer_Adapter_Http();
		$adapter->setDestination($destination)
			->addValidator('Size', false, self::MAX_FILE_SIZE)
			->addValidator('Extension', false, 'jpg,png,gif,jpeg');

		$files = $adapter->getFileInfo();
		$successful = 0;
		foreach ($files as $file => $info) {
			// Skip empty
			if ($info['name'] == null) {
				continue;
			}

			$parts = explode('.', $info['name']);
			$newFilename = md5($info['name']) . '.' . end($parts);
			$imageIdx = preg_replace('/[^\d]/', '', $file);

			try {
				// Rename filter
				$adapter->addFilter('Rename', array(
					'target' => $destination . DIRECTORY_SEPARATOR . $newFilename,
					'overwrite' => true
				));

				// Receive and save
				if ($adapter->receive($file)) {
					$image = new Elm_Model_Plot_Image();
					$image->setData($info);
					$image->setData('exif_data', exif_read_data($info['destination'] . DIRECTORY_SEPARATOR . $newFilename));
					$image->setPlotId($plot->getId())
						->setCaption($params['caption'][$imageIdx]);

					// Set image data
					$image->setThumbnail(self::getImageUrl($plot) . '/' . $newFilename);
					$image->setFull(self::getImageUrl($plot) . '/' . $newFilename);
					$image->save();
					$successful++;
				} else {
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

		if ($successful > 0) {
			$session->addSuccess('Successfully uploaded images');
		}
	}
}