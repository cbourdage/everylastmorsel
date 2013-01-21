<?php

class Elm_Model_Resource_Yield_Transaction extends Colony_Db_Table
{
	protected $_name = 'yields_transactions';

	protected $_primary = 'entity_id';

	/*protected $_referenceMap = array(
		'Yield' => array(
			'columns' => 'yield_id',
			'refTableClass' => 'Elm_Model_Resource_Yield',
			'refColumns' => 'yield_id'
		)
	);*/

	public function getByYieldPurchasableId($ypId)
	{
		$items = array();
		$select = $this->select()->where('purchasable_id = ?', $ypId);
		foreach ($this->fetchAll($select) as $row) {
			$items[] = Elm::getModel('yield/transaction')->load($row->purchasable_id);
		}
		return $items;
	}
}
