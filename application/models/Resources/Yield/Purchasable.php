<?php

class Elm_Model_Resource_Yield_Purchasable extends Colony_Db_Table
{
	protected $_name = 'yields_purchasable';

	protected $_primary = 'entity_id';

	protected $_referenceMap = array(
		'Yield' => array(
			'columns' => 'yield_id',
			'refTableClass' => 'Elm_Model_Resource_Yield',
			'refColumns' => 'yield_id'
		)
	);

	/**
	 * Updates the yields and plot_crops data
	 *
	 * @param Colony_Model_Abstract $object
	 * @return Colony_Model_Abstract|void
	 */
	protected function _afterSave($object)
	{
		// yields
		$totalAvailable = $this->getDefaultAdapter()->fetchOne("SELECT SUM(qty_available) FROM " . $this->_name . " WHERE yield_id = " . $object->getYieldId());
		$forSale = ($totalAvailable == 0) ? false : true;
		$this->getDefaultAdapter()->update(
			'yields',
			array('qty_for_sale' => $totalAvailable, 'is_for_sale' => $forSale),
			'yield_id = ' . $object->getYieldId()
		);

		// plot crops
		$forSale = $this->getDefaultAdapter()->fetchOne("SELECT 1 FROM yields WHERE is_for_sale = 1 AND yield_id = " . $object->getYieldId());
		$this->getDefaultAdapter()->update(
			'plot_crops',
			array('is_for_sale' => $forSale),
			'entity_id = ' . $object->getPlotCropId()
		);
	}
}
