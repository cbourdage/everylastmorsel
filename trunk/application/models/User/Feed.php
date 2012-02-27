<?php


class Elm_Model_User_Feed extends Colony_Model_Abstract
{
	private $_user = null;

	public function _construct()
    {
        $this->_init('plot_status');
    }

	public function getItems($user, $limit)
	{
		$select = $this->_getResource()->select()
			->where('plot_id IN (?)', array_keys($user->getPlotIds()))
			->order('created_at DESC');

		if ($limit) {
			$select->limit($limit);
		}

		$statuses = array();
		foreach ($this->_getResource()->fetchAll($select) as $row) {
			$statuses[] = Bootstrap::getModel('plot_status')->load($row->update_id);
		}

		return $statuses;
	}
}