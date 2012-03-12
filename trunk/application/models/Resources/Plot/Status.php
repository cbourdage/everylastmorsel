<?php

class Elm_Model_Resource_Plot_Status extends Colony_Db_Table
{
    protected $_name = 'plot_status_updates';

	protected $_primary = 'update_id';

	protected $_referenceMap = array(
		'Status' => array(
			'columns' => 'plot_id',
			'refTableClass' => 'Elm_Model_Resource_Plot',
			'refColumns' => 'plot_id'
		)
	);

	/**
	 * Returns status/comments base on plot id
	 *
	 * @param $id
	 * @param null $limit
	 * @return array
	 */
	public function getByPlotId($id, $limit = null)
	{
		$select = $this->select()
			->where('plot_id = ?', $id)
			->where('parent_id IS NULL')
			->order('created_at DESC');

		if ($limit) {
			$select->limit($limit);
		}

		$items = array();
		foreach ($this->fetchAll($select) as $row) {
			$items[] = Bootstrap::getModel('plot_status')->load($row->update_id);
		}

		return $items;
	}

	/**
	 * Returns status/comments base on a users id
	 *
	 * @param $id
	 * @param null $limit
	 * @return array
	 */
	public function getByUserId($id, $limit = null)
	{
		$select = $this->select()
			->where('user_id IN ?', $id)
			->where('parent_id IS NULL')
			->order('created_at DESC');

		if ($limit) {
			$select->limit($limit);
		}

		$items = array();
		foreach ($this->fetchAll($select) as $row) {
			$items[] = Bootstrap::getModel('plot_status')->load($row->update_id);
		}

		return $items;
	}

	/**
	 * Returns the children comments based on parent id
	 *
	 * @param $id
	 * @return array
	 */
	public function getComments($id)
	{
		$select = $this->select()
			->where('parent_id = ?', $id)
			->order('created_at DESC');

		$items = array();
		foreach ($this->fetchAll($select) as $row) {
			$items[] = Bootstrap::getModel('plot_status')->load($row->update_id);
		}

		return $items;
	}

	/**
	 * @param $user
	 * @param null $limit
	 * @return array
	 */
	public function getUserFeed($user, $limit = null)
	{
		$select = $this->select()
			->where('plot_id IN (?)', array_keys($user->getPlotIds()))
			->where('parent_id IS NULL')
			->order('created_at DESC');

		if ($limit) {
			$select->limit($limit);
		}

		$items = array();
		foreach ($this->fetchAll($select) as $row) {
			$items[] = Bootstrap::getModel('plot_status')->load($row->update_id);
		}

		return $items;
	}
}

