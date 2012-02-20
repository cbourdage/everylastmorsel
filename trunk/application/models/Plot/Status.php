<?php


class Elm_Model_Plot_Status extends Colony_Model_Abstract
{
	protected $_user = null;

	protected $_plot = null;

	public function _construct()
    {
        $this->_init('plot_status');
    }

	/**
	 * @param $id
	 * @param null $limit
	 * @return array
	 */
	public function getByPlotId($id, $limit = null)
	{
		$select = $this->_getResource()->select()
			->where('plot_id = ?', $id)
			->order('created_at DESC');

		if ($limit) {
			$select->limit($limit);
		}

		$statuses = array();
		foreach ($this->_getResource()->fetchAll($select) as $row) {
			//Bootstrap::log(get_class_methods(Bootstrap::getModel('plot_status')->load($row->update_id)));
			$statuses[] = Bootstrap::getModel('plot_status')->load($row->update_id);
		}

		return $statuses;
	}

	/**
	 * @return bool
	 */
	public function isValid()
	{
		if (!$this->getPlotId()) {
			return false;
		}

		if (!$this->getUserId()) {
			return false;
		}

		if (!$this->getContent()) {
			return false;
		}

		return true;
	}

	public function getUser()
	{
		if (!$this->_user) {
			$this->_user = Bootstrap::getSingleton('user')->load($this->getUserId());
		}

		return $this->_user;
	}
}