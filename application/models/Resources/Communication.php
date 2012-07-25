<?php

class Elm_Model_Resource_Communication extends Colony_Db_Table
{
    protected $_name = 'communication';

	protected $_primary = 'id';

	public function getByUserId($id)
    {
		$rows = $this->fetchAll(Zend_Db_Table::getDefaultAdapter()->quoteInto('user_to_id=?', $id));
        return $rows;
    }
}

