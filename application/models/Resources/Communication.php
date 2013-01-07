<?php

class Elm_Model_Resource_Communication extends Colony_Db_Table
{
    protected $_name = 'communication';

	protected $_primary = 'id';

	public function getByUserId($id)
    {
		$items = array();
		//$rows = $this->fetchAll(Zend_Db_Table::getDefaultAdapter()->quoteInto('to_user_id = ?', $id));

		$select = $this->select()->where('to_user_id = ?', $id);
		Elm::log($select->__toString());
		foreach ($this->fetchAll($select) as $row) {
			$items[$row->id] = Elm::getModel('communication')->load($row->id);
			$items[$row->id]->setFromUser(Elm::getModel('user')->load($row->from_user_id));
		}

        return $items;
    }
}

