<?php

class Elm_Model_Yield_Purchasable extends Colony_Model_Abstract
{
	/**
	 * @var array
	 */
	private $_types = array();

	public function _construct()
    {
        $this->_init('yield_purchasable');
    }

	public function _beforeSave()
	{
		if (!$this->getYieldId()) {
			$this->setYieldId($this->getYield()->getId());
		}

		if (!$this->getPlotCropId()) {
			$this->setPlotCropId($this->getYield()->getPlotCropId());
		}
	}

	/**
	 * Creates a new status post for current plot
	 *
	 * @return Elm_Model_Plot
	 */
	public function createNewForSaleStatus()
	{
		$plotCrop = $this->getYield()->getPlotCrop();
		$status = Elm::getModel('plot/status');
		$status->setPlotId($plotCrop->getPlotId())
			->setUserId(Elm::getSingleton('user/session')->getUser()->getId())
			->setType('yield')
			->setTitle('For Sale!')
			->setContent(sprintf('%s %s %s are now for sale! Get them while they are fresh.', $this->getQuantity(), $plotCrop->getCrop()->getVariety(), $plotCrop->getCrop()->getType()));
		$status->save();
		return $this;
	}

	/**
	 * Adds a new quantity for sale for a yield based on
	 * the data passed to it.
	 *
	 * @param $yield Elm_Model_Yield
	 * @param $data | Array
	 * @return Elm_Model_Yield_Purchasable
	 */
	public function addQuantityFromYield($yield, $data)
	{
		$this->setData($data);
		$this->setYield($yield)
			->setPlotCropId($yield->getPlotCropId())
			->setYieldId($this->getId())
			->setQuantityUnit($yield->getQuantityUnit());

		$this->save();
		$this->createNewForSaleStatus();
		return $this;
	}
}
