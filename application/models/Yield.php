<?php

class Elm_Model_Yield extends Colony_Model_Abstract
{
	/**
	 * @var array
	 */
	private $_types = array();

	public function _construct()
    {
        $this->_init('yield');
    }

	/**
	 * Returns the crops name
	 *
	 * @return string
	 */
	public function getCropName()
	{
		return $this->getCrop()->getName();
	}

	/**
	 * Returns the crop object
	 *
	 * @return Elm_Model_Crop
	 */
	public function getCrop()
	{
		if (!$this->getData('plot_crop')) {
			$this->setData('plot_crop', Elm::getModel('plot_crop')->load($this->getPlotCropId()));
		}
		return $this->getData('plot_crop');
	}

	/**
	 * Creates a new status post for current plot
	 *
	 * @return Elm_Model_Plot
	 */
	public function createNewYieldStatus()
	{
		$status = Elm::getModel('plot/status');
		$status->setPlotId($this->getId())
			->setUserId($this->getUserId())
			->setType('yield')
			->setTitle('New Yield Added!')
			->setContent(sprintf('%s yielded %s!', $this->getCropName(), $this->getQuantity()));
		$status->save();
		return $this;
	}

}
