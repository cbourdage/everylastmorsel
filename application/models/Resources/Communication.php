<?php

class Elm_Model_Resource_Communication extends Colony_Db_Table
{
    protected $_name = 'communication';

	protected $_primary = 'id';

	protected function _afterLoad($object)
	{
		$object->setToUser(Elm::getModel('user')->load($object->getToUserId()));
		$object->setFromUser(Elm::getModel('user')->load($object->getFromUserId()));
		return $this;
	}

	public function getAll($id)
    {
		$items = array();
		$select = $this->select()
			->where('to_user_id = ?', $id)
			->order('created_at desc');
		foreach ($this->fetchAll($select) as $row) {
			$items[$row->id] = Elm::getModel('communication')->load($row->id);
		}

        return $items;
    }

	public function getFiltered($object)
    {
		$items = array();
		$select = $this->select();

		switch(strtolower($object->getFilterBy())) {
			case 'inbox':
				$select->where('to_user_id = ?', $object->getUserId())
					->where('is_archived = ?', 0);
				break;
			case 'archive':
				$select->where('to_user_id = ?', $object->getUserId())
					->where('is_archived = ?', 1);
				break;
			case 'sent':
				$select->where('from_user_id = ?', $object->getUserId());
				break;
			default:
				$select->where('to_user_id = ?', $object->getUserId());
				break;
		}

		$select->order('created_at desc');
		foreach ($this->fetchAll($select) as $row) {
			$items[$row->id] = Elm::getModel('communication')->load($row->id);
		}

        return $items;
    }
}

