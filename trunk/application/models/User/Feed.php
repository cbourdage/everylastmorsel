<?php


class Elm_Model_User_Feed extends Colony_Model_Abstract
{
	private $_user = null;

	protected $_feed = array();

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
		if (count($this->_feed) < 1 && count($user->getPlotIds()) > 0) {
			$this->_feed = $this->_getResource()->getUserFeed($user);
		}
		return $this->_feed;
	}
}