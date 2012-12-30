<?php

class Elm_Model_Resource_Crop extends Colony_Db_Table
{
	protected $_name = 'crops';

	protected $_primary = 'crop_id';

	/**
	 * Loads crops based on name
	 *
	 * @param Elm_Model_Crop $obj
	 * @param $name
	 * @return Elm_Model_Resource_Crop
	 */
	public function loadByName(Elm_Model_Crop $obj, $name)
	{
		$row = $this->fetchRow($this->getDefaultAdapter()->quoteInto('name = ?', $name));
        if ($row !== null) {
            $this->load($obj, $row->crop_id);
        } else {
            $obj->setData(array());
        }
        return $this;
	}

	/**
	 * @return array
	 */
	public function getCrops()
	{
		$items = array();
		$select = $this->select(); //->where('is_active', '1');
		foreach ($this->fetchAll($select) as $row) {
			$items[] = Elm::getModel('crop')->load($row->crop_id);
		}
		return $items;
	}

	/**
	 * @return array
	 */
	public function uniqueTypes()
	{
		$items = array();
		$select = $this->select()->distinct()
			->from($this->_name, 'type');
		foreach ($this->fetchAll($select) as $row) {
			$items[] = $row->type;
		}
		return $items;
	}

	public function getDefaultVarieties($type=null)
	{
		$items = array();
		$select = $this->select()
			->where('type = ?', new Zend_Db_Expr('LOWER(variety)'));

		if ($type !== null) {
			$select->where('type = ?', $type);
		}

		foreach ($this->fetchAll($select) as $row) {
			$items[$row->crop_id] = Elm::getModel('crop')->load($row->crop_id);
		}

		return $items;
	}

	/**
	 * @param string $term
	 * @param null|string $type
	 * @param null|int $limit
	 * @return array
	 */
	public function searchVarieties($term, $type=null, $limit=null)
	{
		$items = array();
		$select = $this->select()
			->where('variety LIKE ?', "%$term%");

		if ($type !== null) {
			$select->where('type = ?', $type);
		}

		if ($limit !== null) {
			$select->limit($limit, 0);
		}

		foreach ($this->fetchAll($select) as $row) {
			$items[$row->crop_id] = Elm::getModel('crop')->load($row->crop_id);
		}
		return $items;
	}
}
