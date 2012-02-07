<?php


class Elm_Model_Plot extends Colony_Model_Abstract
{
	private $_users = array();

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

	public function getAllPlots()
	{
		return $this->_getResource()->getAllPlots();
	}

	/**
	 * Get users
	 *
	 * @return mixed
	 */
	public function getUsers()
	{
		if (count($this->_users) < 1) {
			foreach ($this->getUserIds() as $id => $role) {
				if ($role != Elm_Model_Resource_Plot::ROLE_CREATOR) {
					$user = Bootstrap::getModel('user')->load($id);
					$user->setUserRole($role);
					$this->_users[] = $user;
				}
			}
		}
		return $this->_users;
	}

    /**
     * Send email with new account specific information
     *
	 * @TODO send new plot email - http://stackoverflow.com/questions/1218191/how-can-i-make-email-template-in-zend-framework
	 *
	 * @param string $backUrl
     * @return Elm_Model_User
     */
    public function sendNewPlotEmail($backUrl = '')
    {
		try {
			$EmailTemplate = new Elm_Model_Email_Template(array('template' => 'new-plot.phtml'));
			$EmailTemplate->setParams(array(
				'plot' => $this
			));
			$EmailTemplate->send(array('email' => 'collin.bourdage@gmail.com', 'name' => 'Collin Bourdage'));
		} catch(Exception $e) {
			Bootstrap::logException($e);
		}
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

	/**
	 * Checks if the user is associated with this plot
	 *
	 * @param $user
	 * @return bool
	 */
	public function isAssociated($user)
	{
		foreach ($this->getUsers() as $u) {
			if ($user->getId() == $u->getId()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns a plots url
	 *
	 * @return string
	 */
	public function getUrl()
	{
		$url = '/p/' . $this->getId();
		return $url;
	}
}