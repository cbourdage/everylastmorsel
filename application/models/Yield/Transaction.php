<?php

class Elm_Model_Yield_Transaction extends Colony_Model_Abstract
{
	const REASON_PURCHASE = 'Purchase Request';

	/**
	 * @var Elm_Model_Yield_Purchasable
	 */
	private $_purchasable = null;

	public function _construct()
    {
        $this->_init('yield_transaction');
    }

	/**
	 * Gets all transactions by a specified yields purchsasable object
	 *
	 * @param Elm_Model_Yield_Purchasable $yp
	 * @return mixed
	 */
	public function getByYieldPurchasable($yp)
	{
		return $this->_getResource()->getByYieldPurchasableId($yp->getId());
	}

	/**
	 * Checks if transaction was purchased
	 *
	 * @return bool
	 */
	public function wasPurchased()
	{
		return (bool) $this->getIsSale();
	}

	/**
	 * Validates the current transaction object data
	 *
	 * @return bool
	 */
	public function validate()
	{
		if (!$this->getPurchasableId()) {
			Elm::throwException('Unable to purchase from a non-yield');
			return false;
		}

		$purchasableObj = Elm::getModel('yield/purchasable')->load($this->getPurchasableId());
		if ((int) $this->getQuantity() > (int) $purchasableObj->getQtyAvailable()) {
			Elm::throwException(sprintf('Invalid purchase quantity. There are only %s available.', $purchasableObj->getQtyAvailable()));
			return false;
		}

		return true;
	}

	/**
	 * Returns the transaction link for current transaction
	 * if it exists.
	 *
	 * @return null
	 */
	public function getLink()
	{
		if ($this->_transLink) {
			return $this->_transLink;
		}

		if ($this->wasPurchased()) {
			$this->_transLink = Elm::getModel('yield/transactionLink')->loadByTransactionId($this->getId());
			return $this->_transLink;
		}
		return null;
	}

	/**
	 * @return Elm_Model_Yield_Purchasable
	 */
	public function getPurchasableObject()
	{
		if (!$this->_purchasable) {
			$this->_purchasable = Elm::getModel('yield/purchasable')->load($this->getPurchasableId());
		}
		return $this->_purchasable;
	}

	/**
	 * @param $data
	 * @return Elm_Model_Yield_Transaction
	 * @throws Exception
	 */
	public function purchase($data)
	{
		// prepare new purchase
		$this->createNew($data, self::REASON_PURCHASE);

		// save the link on a new purchase
		Elm::getModel('yield/transactionLink')->linkThemUp($this);
		return $this;
	}

	/**
	 * @param $data
	 * @param $type
	 * @return Elm_Model_Yield_Transaction
	 */
	public function createNew($data, $type)
	{
		$purchasableObj = $this->getPurchasableObject();
		$plotCrop = $purchasableObj->getPlotCrop();

		$this->setQuantityUnit($purchasableObj->getQuantityUnit());
		$this->setTotal($this->getQuantity() * (int) $purchasableObj->getPrice());
		$this->setYieldId($purchasableObj->getYield()->getId());
		$this->setPlotCropId($plotCrop->getId());
		$this->setCropId($plotCrop->getPlot()->getId());
		$this->setReason($type);
		$this->save();

		switch(self::REASON_PURCHASE) {
			case self::REASON_PURCHASE :
				$purchasableObj->setQtyAvailable((int) $purchasableObj->getQtyAvailable() - (int) $this->getQuantity());

				// Check still available
				if ($purchasableObj->getQtyAvailable() === 0) {
					Elm::log('setting sold out with available: ' . $purchasableObj->getQtyAvailable());
					$purchasableObj->setIsSoldOut(true);
				}

				$purchasableObj->save();
				break;
		}
		return $this;
	}
}
