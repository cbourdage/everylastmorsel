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

	public function getCrops()
	{
		$items = array();
		$select = $this->select(); //->where('is_active', '1');
		foreach ($this->fetchAll($select) as $row) {
			$items[] = Elm::getModel('crop')->load($row->crop_id);
		}

		return $items;
	}
}
