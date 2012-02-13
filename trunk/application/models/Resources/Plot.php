<?php

class Elm_Model_Resource_Plot extends Colony_Db_Table
{
	const RELATIONSHIP_TABLE = 'user_plot_relationships';
	const ROLE_CREATOR = 'Creator';
	const ROLE_OWNER = 'Owner';
	const ROLE_GARDENER = 'Gardener';

	public static $userRoles = array('Creator', 'Owner', 'Gardener');
	public static $plotTypes = array('Community', 'Personal');

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
		
		$select = $this->getDefaultAdapter()
			->select()
			->from(self::RELATIONSHIP_TABLE)
			->where('plot_id=?', $object->getId());
		if ($rows = $this->getDefaultAdapter()->fetchAll($select)) {
			$users = array();
			foreach ($rows as $row) {
				$users[$row->user_id] = $row->role;
			}
			$object->setUserIds($users);
		}
		else {
			$object->setUserIds(null);
		}

		return $this;
	}

	public function getAllPlots()
	{
		$select = $this->select(); //->where('is_active', '1');
		if ($rows = $this->fetchAll($select)) {
			return $rows;
		}
		return null;
	}

	public function getImages($plotId)
	{
		$return = array();
		$row = $this->find($plotId)->current();
		$images = $row->findDependentRowset('Elm_Model_Resource_Plot_Image', 'Image');
		foreach ($images as $img) {
			$return[$img->image_id] = Bootstrap::getModel('plot_image')->load($img->image_id);
		}

		return $return;
	}

	public function loadByLatLong($object, $lat, $long)
	{
	}

	public function associateUser($object, $userId, $role)
	{
		if (in_array($role, self::$userRoles)) {
				$this->getDefaultAdapter()->insert(
					self::RELATIONSHIP_TABLE,
					array('user_id' => $userId, 'plot_id' => $object->getId(), 'role' => $object->getRole())
				);
			}
	}
}

