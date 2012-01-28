<?php


class Elm_Model_Plot extends Colony_Model_Abstract
{
	public function _construct()
    {
        $this->_init('plot');
    }

	public function _beforeSave()
	{
	}

	/**
     * Load plot by lat & long
     *
     * @param   string $lat
     * @param   string $long
     * @return  Elm_Model_Plot
     */
    public function loadByLatLong($lat, $long)
    {
        $this->_getResource()->loadByLatLong($this, $lat, $long);
        return $this;
    }

    /**
     * Send email with new account specific information
     *
	 * @TODO send new plot email
	 *
	 * @param string $backUrl
     * @return Elm_Model_User
     */
    public function sendNewPlotEmail($backUrl = '')
    {
        //http://stackoverflow.com/questions/1218191/how-can-i-make-email-template-in-zend-framework
		Bootstrap::log(__METHOD__);
        return $this;
    }

	/**
	 * Associates a user and a plot with an assigned role
	 *
	 * @param $userId int
	 * @param $role string
	 * @return Elm_Model_Plot
	 */
	public function associateUser($userId, $role)
	{
		$this->_getResource()->associateUser($this, $userId, $role);
        return $this;
	}

	public function getUrl()
	{
		$url = '/p/' . $this->getId();
		return $url;
	}
}