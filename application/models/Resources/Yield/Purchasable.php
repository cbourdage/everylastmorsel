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
}
