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

	/**
	 * Updates the necessary ids and quantity available
	 *
	 * @return Colony_Model_Abstract|void
	 */
	public function _beforeSave()
	{
		if (!$this->getYieldId()) {
			$this->setYieldId($this->getYield()->getId());
		}

		if (!$this->getPlotCropId()) {
			$this->setPlotCropId($this->getYield()->getPlotCropId());
		}

		if (!$this->getQtyAvailable() && !$this->getIsSoldOut()) {
			$this->setQtyAvailable($this->getQuantity());
		} else {
			// @TODO update the quantities based on the transaction totals
			//$this->setQtyAvailable($this->getQtyAvailable() + $this->getQuantity());
		}
	}

	/**
	 * Returns the yield object
	 *
	 * @return Elm_Model_Yield|mixed
	 */
	public function getYield()
	{
		if (!$this->getData('yield')) {
			$this->setData('yield', Elm::getModel('yield')->load($this->getYieldId()));
		}
		return $this->getData('yield');
	}

	/**
	 * Returns the plot crop object
	 *
	 * @return Elm_Model_Crop
	 */
	public function getPlotCrop()
	{
		if (!$this->getData('plot_crop')) {
			$this->setData('plot_crop', Elm::getModel('plot/crop')->load($this->getPlotCropId()));
		}
		return $this->getData('plot_crop');
	}

	/**
	 * Returns the crop object (this is the main crop info)
	 * @return mixed
	 */
	public function getCrop()
	{
		return $this->getPlotCrop()->getCrop();
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

	/**
	 * Un-lists the purchasable for sale and updates the yields'
	 * qty for sale value
	 *
	 * @return Elm_Model_Yield_Purchasable
	 */
	public function unList()
	{
		if ($this->getIsForSale()) {
			$this->setIsForSale(false)->save();
			$yield = $this->getYield();
			$yield->setQtyForSale($yield->getQtyForSale() - $this->getQtyAvailable());
		}
		return $this;
	}

	/**
	 * @return Elm_Model_Yield_Purchasable
	 */
	public function listForSale()
	{
		if (!$this->getIsForSale()) {
			$this->setIsForSale(true)->save();
			$yield = $this->getYield();
			$yield->setQtyForSale($yield->getQtyForSale() + $this->getQtyAvailable());
		}
		return $this;
	}
}
