<?php

class Elm_Model_Resource_Plot extends Colony_Db_Table
{
	const RELATIONSHIP_TABLE = 'user_plot_relationships';
	const ROLE_CREATOR = 'Creator';
	const ROLE_OWNER = 'Owner';
	const ROLE_GARDENER = 'Gardener';
	const ROLE_WATCHER = 'Watcher';

	public static $userRoles = array('Creator', 'Owner', 'Gardener', 'Watcher');
	public static $plotTypes = array('Community' => array('Individual', 'Group'), 'Personal', 'Farm');

    protected $_name = 'plot';

	protected $_primary = 'plot_id';

	protected function _afterSave($object)
	{
		parent::_afterSave($object);

		if ($object->getUserId() && !$this->isUserAssociated($object, $object->getUserId(), self::ROLE_CREATOR)) {
			$this->associateUser($object, $object->getUserId(), self::ROLE_CREATOR, true);
		}
		return $this;
	}

	/**
	 * @param $object
	 * @return Elm_Model_Resource_Plot
	 */
	protected function _afterLoad($object)
	{
		parent::_afterLoad($object);
		
		$select = $this->getDefaultAdapter()->select()
			->from(self::RELATIONSHIP_TABLE)
			->where('plot_id = ?', $object->getId())
			->where('is_approved = 1');

		if ($rows = $this->getDefaultAdapter()->fetchAll($select)) {
			$users = array();
			foreach ($rows as $row) {
				$users[$row->user_id][] = array(
					'role' => $row->role,
					'is_approved' => $row->is_approved
				);
			}
			$object->setUserIds($users);
		} else {
			$object->setUserIds(null);
		}

		return $this;
	}

	/**
	 * @param $object
	 * @return array
	 */
	public function getPendingUsers($object)
	{
		$select = $this->getDefaultAdapter()->select()
			->from(self::RELATIONSHIP_TABLE)
			->where('plot_id = ?', $object->getId())
			->where('is_approved = 0');

		$users = array();
		if ($rows = $this->getDefaultAdapter()->fetchAll($select)) {
			foreach ($rows as $row) {
				$users[$row->user_id][] = array(
					'role' => $row->role,
					'is_approved' => $row->is_approved
				);
			}
		}

		return $users;
	}

	/**
	 * Returns array of all plots
	 *
	 * @return array
	 */
	public function getAllPlots()
	{
		$items = array();
		$select = $this->select()->where('is_active', '1');
		foreach ($this->fetchAll($select) as $row) {
			$items[] = Elm::getModel('plot')->load($row->plot_id);
		}

		return $items;
	}

	/**
	 * @param $plotId
	 * @return array
	 */
	public function getImages($plotId)
	{
		$items = array();
		$rows = $this->find($plotId)->current()
			->findDependentRowset('Elm_Model_Resource_Plot_Image', 'Image');
		foreach ($rows as $row) {
			$items[$row->image_id] = Elm::getModel('plot_image')->load($row->image_id);
		}

		return $items;
	}

	/**
	 * @param $plotId
	 * @return array
	 */
	public function getCrops($plotId)
	{
		$items = array();
		$rows = $this->find($plotId)->current()
			->findDependentRowset('Elm_Model_Resource_Plot_Crop', 'Crop');
		foreach ($rows as $row) {
			$items[$row->entity_id] = Elm::getModel('plot_crop')->load($row->entity_id);
		}

		return $items;
	}

	public function loadByLatLong($object, $lat, $long)
	{
	}

	/**
	 * Checks if a user is already associated with a plot with a specific role
	 *
	 * @param $object
	 * @param $userId
	 * @param $role
	 * @param int $active
	 * @return bool
	 */
	public function isUserAssociated($object, $userId, $role = null, $active = 0)
	{
		$exists = null;
		$where = "WHERE plot_id = {$object->getId()} AND user_id = {$userId}";
		if (is_array($role)) {
			$where .= " AND role IN ('" . implode("','", $role) . "')";
		} elseif (in_array($role, self::$userRoles)) {
			$where .= " AND role = '{$role}'";
		}

		if ($active == 1) {
			$where .= " AND is_approved = 1";
		}

		$exists = $this->getDefaultAdapter()->fetchOne("SELECT 1 FROM " . self::RELATIONSHIP_TABLE . " $where");
		return !empty($exists);
	}

	/**
	 * @param $object
	 * @param $userId
	 * @param $role
	 * @param $approved
	 */
	public function associateUser($object, $userId, $role, $approved)
	{
		if (in_array($role, self::$userRoles)) {
			$this->getDefaultAdapter()->insert(
				self::RELATIONSHIP_TABLE,
				array('user_id' => $userId, 'plot_id' => $object->getId(), 'role' => $role, 'is_approved' => $approved)
			);
		}
	}

	/**
	 * @param $object
	 * @param $userId
	 * @param $role
	 * @param $isApproved
	 */
	public function updateAssociatedUser($object, $userId, $role, $isApproved)
	{
		if (in_array($role, self::$userRoles)) {
			$this->getDefaultAdapter()->update(
				self::RELATIONSHIP_TABLE,
				array('is_approved' => $isApproved),
				"plot_id = {$object->getId()} AND user_id = {$userId} AND role = '{$role}'"
			);
		}
	}
}

