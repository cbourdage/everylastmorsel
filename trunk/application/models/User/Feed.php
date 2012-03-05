<?php


class Elm_Model_User_Feed extends Colony_Model_Abstract
{
	private $_user = null;

	public function _construct()
    {
        $this->_init('plot_status');
    }

	/**
	 * @param $user
	 * @param $limit
	 * @return array
	 */
	public function getItems($user, $limit)
	{
		$comments = array();
		if (count($user->getPlotIds()) > 0) {
			$select = $this->_getResource()->select()
				->where('plot_id IN (?)', array_keys($user->getPlotIds()))
				->order('created_at DESC');

			if ($limit) {
				$select->limit($limit);
			}

			foreach ($this->_getResource()->fetchAll($select) as $row) {
				$comments[] = Bootstrap::getModel('plot_status')->load($row->update_id);
			}
		}
		return $comments;
	}
}