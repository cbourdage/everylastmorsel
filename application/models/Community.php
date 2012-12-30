<?php


class Elm_Model_Community extends Colony_Model_Abstract
{
	private $_users = array();

	private $_plots = array();

	private $_crops = array();

	public function _construct()
    {
        $this->_resource = Zend_Db_Table::getDefaultAdapter();
    }

	public function getUsers()
	{
		if (count($this->_users) < 1) {
			// @TODO add is_active to user table
			$results = $this->_resource->fetchAll('SELECT user_id FROM user');
			foreach ($results as $row) {
				$this->_users[] = Elm::getModel('user')->load($row->user_id);
			}
		}
		return $this->_users;
	}

	public function getPlots()
	{
		if (count($this->_plots) < 1) {
			$results = $this->_resource->fetchAll('SELECT plot_id FROM plot');
			foreach ($results as $row) {
				$this->_plots[] = Elm::getModel('plot')->load($row->plot_id);
			}
		}
		return $this->_plots;
	}

	public function getCropsOnSale()
	{
		if (count($this->_crops) < 1) {
			$results = $this->_resource->fetchAll('SELECT yield_id FROM yields WHERE is_for_sale = 1');
			foreach ($results as $row) {
				$this->_crops[] = Elm::getModel('yield')->load($row->yield_id);
			}
		}
		return $this->_crops;
	}
}