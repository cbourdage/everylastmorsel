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
		$object->setDatePicked(trim(strstr($object->getDatePicked(), ' ', true)));
		return $this;
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
