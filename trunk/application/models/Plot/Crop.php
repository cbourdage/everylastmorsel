<?php

class Elm_Model_Plot_Crop extends Colony_Model_Abstract
{
	/**
	 * Available crop starting types
	 *
	 * @var array
	 */
	public static $startingType = array(
		'seed',
		'seedling',
		'plant'
	);

	/**
	 * Available crop conditions
	 *
	 * @var array
	 */
	public static $conditions = array(
		'sunny',
		'partial_sun'
	);

	private $_crop;

	/**
	 * Constructor
	 */
	public function _construct()
    {
        $this->_init('plot_crop');
    }

	/**
	 * Sets the crop id if it has not yet been set
	 *
	 * @return Colony_Model_Abstract
	 */
	protected function _beforeSave()
	{
		if (!$this->getCropId()) {
			$this->setCropId($this->_crop->getId());
		}

		if (!$this->getCreatedAt()) {
			$date = new Zend_Date($this->getDatePlanted());
			$this->setDatePlanted($date->toString('YYYY-MM-dd'));
		}

		return parent::_beforeSave();
	}

	protected function _afterLoad()
	{
		$this->_crop = Elm::getModel('crop')->load($this->getCropId());
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCrop()
	{
		return $this->_crop;
	}

	/**
	 * Extracts form data and creates object
	 *
	 * @param $data
	 * @return Elm_Model_Plot_Crop
	 */
	public function extractData($data)
	{
		foreach ($data as $key => $value) {
			$this->setData($key, $value);
		}

		if (!$this->getUserId()) {
			$this->setUserId(Elm::getSingleton('user/session')->getUser()->getId());
		}

		if (!$this->_crop) {
			$crop = new Elm_Model_Crop();
			if (!$this->getCropId()) {
				//if (!$crop->lookupLoad($this->getCropName())) {
					$crop->setData($data);
					$crop->save();
				//}
			} else {
				$crop->load($this->getCropId());
			}

			$this->_crop = $crop;
		}

		return $this;
	}
}
