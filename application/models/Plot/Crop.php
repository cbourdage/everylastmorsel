<?php

class Elm_Model_Plot_Crop extends Colony_Model_Abstract
{
	public static $coverageUnits = array(
		'individual' => '# of plants',
		'sq_ft' => 'square feet'
	);

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

	/**
	 * Stores the plots_crops crop object
	 *
	 * @var Elm_Model_Crop
	 */
	private $_crop = null;

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
	 * Creates a new status post for current plot
	 *
	 * @return Elm_Model_Plot
	 */
	public function createNewCropStatus()
	{
		$user = Elm::getModel('user')->load($this->getUserId());
		$status = Elm::getModel('plot/status');
		$status->setPlotId($this->getPlotId())
			->setUserId($this->getUserId())
			->setType('crop')
			->setTitle('New Crop Added!')
			->setContent(sprintf(
				'<a href="%s">%s</a> has planted %s %s!',
				$user->getUrl(),
				$user->getName(),
				$this->getQuantity(),
				$this->getCrop()->getName()
			));
		$status->save();
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
			$crop->load($this->getCropId());
			$this->_crop = $crop;
		}

		return $this;
	}

	/**
	 * Returns the covereage units in readable format
	 *
	 * @return mixed|string
	 */
	public function getCoverageUnits()
	{
		$units = $this->getData('coverage_unit');
		switch ($units) {
			case 'individual':
				$units = 'plant';
				break;
			case 'sq_ft':
			default:
				$units = 'sq/ft';
				break;
		}
		return $units;
	}
}
