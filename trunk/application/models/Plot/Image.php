<?php


class Elm_Model_Plot_Image extends Colony_Model_Abstract
{
	const LOG_FILE = 'plot-images.log';
	const DESTINATION_DIR = 'http/media/plot';

	public function _construct()
    {
        $this->_init('plot_image');
    }

	/**
	 * @static
	 * @param $plot
	 * @return string
	 */
	public static function getPlotImagePath($plot)
	{
		if ($plot->getId() > 9) {
			return DIRECTORY_SEPARATOR . substr($plot->getId(), 0, 1) . DIRECTORY_SEPARATOR . substr($plot->getId(), 1, 1);
		}
		return DIRECTORY_SEPARATOR . substr($plot->getId(), 0, 1);
	}

	/**
	 * @static
	 * @param $plot
	 * @return mixed
	 */
	public static function getPlotImageUrl($plot)
	{
		return str_replace(DIRECTORY_SEPARATOR, '/', self::getPlotImagePath($plot));
	}
}