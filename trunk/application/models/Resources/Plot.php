<?php

class Elm_Model_Resource_Plot extends Colony_Db_Table
{
	const RELATIONSHIP_TABLE = 'user_plot_relationships';

    protected $_name = 'plot';

	protected $_primary = 'plot_id';

	protected function _afterSave($object)
	{
		parent::_afterSave($object);

		if ($object->getUserId()) {
			if ($object->getIsNewPlot()) {
				$this->getDefaultAdapter()->insert(
					self::RELATIONSHIP_TABLE,
					array('user_id' => $object->getUserId(), 'plot_id' => $object->getId(), 'role' => 'Creator')
				);
				$object->setIsNewPlot(false);
			}
		}

		return $this;
	}

	// load all users
	protected function _afterLoad($object)
	{
		parent::_afterLoad($object);
		
		$select = $this->getDefaultAdapter()->select()
			->from(self::RELATIONSHIP_TABLE)
			->where('plot_id =?', $object->getId());
		if ($rows = $this->getDefaultAdapter()->fetchAll($select)) {
			$users = array();
			foreach ($rows as $row) {
				$users[] = Bootstrap::getModel('user')->load($row->user_id, false);
			}
			$object->setUsers($users);
		}
		else {
			$object->setUsers(null);
		}
		
		return $this;
	}

	public function loadByLatLong($obj, $lat, $long)
	{
	}
}

