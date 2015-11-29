<?php

class Elm_Model_Yield extends Colony_Model_Abstract
{
	/**
	 * @var array
	 */
	public static $yieldUnits = array(
		'individual' => 'individual quantity',
		'bundles' => 'bundles'
	);

	public function _construct()
    {
        $this->_init('yield');
    }

	protected function _afterLoad()
	{

	}

	/**
	 * Sets the crop id if it has not yet been set
	 *
	 * @return Colony_Model_Abstract
	 */
	protected function _beforeSave()
	{
		if (!$this->getCreatedAt()) {
			$date = new Zend_Date($this->getDatePicked());
			$this->setDatePicked($date->toString('YYYY-MM-dd'));
		}

		return parent::_beforeSave();
	}

	/**
	 * @param Elm_Model_Plot $plot
	 * @return mixed
	 */
	public function fetchByPlot($plot)
	{
		$yields = $this->_getResource()->fetchByPlot($plot);
		return $yields;
	}

	/**
	 * @param $plotCrop
	 * @return mixed
	 */
	public function fetchByPlotCrop($plotCrop)
	{
		$yields = $this->_getResource()->fetchByPlotCrop($plotCrop);
		return $yields;
	}

	/**
	 * Returns the quantity units in a readable format
	 *
	 * @return string
	 */
	public function getQuantityUnits()
	{
		$units = $this->getData('quantity_unit');
		$helper = new Elm_View_Helper_Yields();
		return $helper->formatUnits($units, $this->getQuantity());
	}

	/**
	 * Returns the crop object
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
	 * Creates a new status post for current plot
	 *
	 * @return Elm_Model_Plot
	 */
	public function createNewYieldStatus()
	{
		$status = Elm::getModel('plot/status');
		$status->setPlotId($this->getPlotCrop()->getPlotId())
			->setUserId(Elm::getSingleton('user/session')->getUser()->getId())
			->setType('yield')
			->setTitle('New Yield Added!')
			->setContent(sprintf('%s yielded %s!', $this->getPlotCrop()->getCrop()->getVariety(), $this->getQuantity()));
		$status->save();
		return $this;
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
		return $this;
	}

	/**
	 * prepares post data and adds yields
	 *
	 * @param $data
	 * @return Elm_Model_Yield
	 */
	public function prepareNewYield($data)
	{
		$this->extractData($data);
		$this->save();

		if (isset($data['purchasable']) && (strlen($data['purchasable']['quantity']) > 0 && strlen($data['purchasable']['price']) > 0)) {
			$this->makePurchasable($data['purchasable']);
		} else {
			$this->createNewYieldStatus();
		}

		return $this;
	}

	public function prepareYieldUpdates($data)
	{
		//$this->save();
		return $this;
	}

	/**
	 * Adds new purchasable yields.
	 *
	 * @TODO validate against sale quantities
	 *
	 * @param array $data
	 * @return Elm_Model_Yield
	 */
	public function makePurchasable($data)
	{
		$newForSaleQty = (int)$this->getQtyForSale() + (int)$data['quantity'];

		if ($newForSaleQty > $this->getQuantity()) {
			Elm::throwException("Looks like you're trying to sell more than you've yielded.");
			return;
		}

		$purchasable = new Elm_Model_Yield_Purchasable();
		$purchasable->addQuantityFromYield($this, $data);

		// update the purchasable info
		$temp = $this->getData('purchasable');
		if (!is_array($temp)) {
			$temp = array();
		}
		array_push($temp, $purchasable);
		$this->setData('purchasable', $temp);

		// Update data of yield
		$this->setIsForSale(true)->setQtyForSale($newForSaleQty)->save();

		// update plot crop to flag for sale
		$this->getPlotCrop()->setIsForSale(true)->save();

		return $this;
	}

	/**
	 * Flags yield !for sale
	 *
	 * @return Elm_Model_Yield
	 */
	public function cancelPurchasable()
	{
		$this->setIsForSale(false);
		$this->save();

		return $this;
	}
}
