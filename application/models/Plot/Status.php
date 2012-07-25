<?php


class Elm_Model_Plot_Status extends Colony_Model_Abstract
{
	protected $_user = null;

	protected $_plot = null;

	protected $_comments = array();

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
		return $this->_getResource()->getByPlotId($id, $limit);
	}

	/**
	 * @param $id
	 * @param null $limit
	 * @return array
	 */
	public function getByUserId($id, $limit = null)
	{
		return $this->_getResource()->getByUserId($id, $limit);
	}

	/**
	 * @return mixed
	 */
	public function getComments()
	{
		if (count($this->_comments) < 1) {
			$this->_comments = $this->_getResource()->getComments($this->getId());
			$this->_comments = array_reverse($this->_comments, true);
		}
		return $this->_comments;
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
			$this->_user = Elm::getSingleton('user')->load($this->getUserId());
		}

		return $this->_user;
	}
}