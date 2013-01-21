<?php

class Elm_Model_Resource_Yield extends Colony_Db_Table
{
	protected $_name = 'yields';

	protected $_primary = 'yield_id';

	protected $_referenceMap = array(
		'Yield' => array(
			'columns' => 'plot_crop_id',
			'refTableClass' => 'Elm_Model_Resource_Plot_Crop',
			'refColumns' => 'crop_id'
		)
	);

	protected function _afterLoad($object)
	{
		// set date object
		$object->setDatePicked(trim(substr($object->getDatePicked(), 0, strpos($object->getDatePicked(), ' '))));

		$items = array();
		$purchasable = $this->find($object->getId())->current()
			->findDependentRowset('Elm_Model_Resource_Yield_Purchasable', 'Yield');
		foreach ($purchasable as $p) {
			$items[$p->entity_id] = Elm::getModel('yield/purchasable')->load($p->entity_id);
		}
		$object->setPurchasable($items);
		return $this;
	}

	/**
	 * Updates the plot_crops data
	 *
	 * @param Colony_Model_Abstract $object
	 * @return Colony_Model_Abstract|void
	 */
	protected function _afterSave($object)
	{
		$forSale = $this->getDefaultAdapter()->fetchOne("SELECT 1 FROM " . $this->_name . " WHERE is_for_sale = 1 AND yield_id = " . $object->getYieldId());

		Elm::log("forSale: " . $forSale);
		$this->getDefaultAdapter()->update(
			'plot_crops',
			array('is_for_sale' => $forSale),
			'entity_id = ' . $object->getPlotCropId()
		);
	}

	public function fetchByPlot($plot)
	{
		$items = array();
		$select = $this->select()
			->from(array('y' => $this->_name))
			->join(array('pc' => 'plot_crops'), 'pc.entity_id = y.plot_crop_id', array())
			->where('pc.plot_id = ?', $plot->getId());
		foreach ($this->fetchAll($select) as $row) {
			$items[$row->yield_id] = Elm::getModel('yield')->load($row->yield_id);
		}

		return $items;
	}

	public function fetchByPlotCrop($plotCrop)
	{
		$items = array();
		$select = $this->select()->where('plot_crop_id = ?', $plotCrop->getId());
		foreach ($this->fetchAll($select) as $row) {
			$items[$row->yield_id] = Elm::getModel('yield')->load($row->yield_id);
		}

		return $items;
	}
}
