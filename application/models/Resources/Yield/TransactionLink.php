<?php

class Elm_Model_Resource_Yield_TransactionLink extends Colony_Db_Table
{
	protected $_name = 'yields_transactions_link';

	protected $_primary = 'entity_id';

	/*protected $_referenceMap = array(
		'Yield' => array(
			'columns' => 'yield_id',
			'refTableClass' => 'Elm_Model_Resource_Yield',
			'refColumns' => 'yield_id'
		)
	);*/

	public function loadByTransactionId($transId)
	{
		$items = array();
		$select = $this->select()->where('transaction_id = ?', $transId);
		$row = $this->fetchRow($select);
		return Elm::getModel('yield/transactionLink')->load($row->entity_id);
	}
}
