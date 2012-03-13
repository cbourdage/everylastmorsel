<?php

class Elm_View_Helper_Plot extends Zend_View_Helper_Abstract
{
	private $_session = null;

	/**
	 * @return Elm_View_Helper_Plot
	 */
	public function Plot()
	{
		$this->_session = Bootstrap::getSingleton('user/session');
		return $this;
	}

	public function getPlot()
	{
		if (Zend_Registry::isRegistered('current_plot')) {
			return Zend_Registry::get('current_plot');
		}
		return null;
	}

	/**
	 * @param $plot
	 * @param int $count
	 * @return mixed
	 */
	public function getFeed($plot, $count=10)
	{
		$feed = $plot->getFeed($count);
		return $feed;
	}

	public function filterUsers($users)
	{
		if (!$this->_session->isLoggedIn()) {
			foreach ($users as $key => $user) {
				foreach ($user->getData('user_role') as $role) {
					if ($role['is_approved'] > 0) {
						continue;
					}

					if ($role['is_approved'] < 1 && (
							$role['role'] != Elm_Model_Resource_Plot::ROLE_CREATOR
							&& $role['role'] != Elm_Model_Resource_Plot::ROLE_WATCHER
					)) {
						unset($users[$key]);
					}
				}
			}
		}

		return $users;
	}

	/**
	 * Returns string url for user approval
	 *
	 * @param $plot
	 * @param $user
	 * @return mixed
	 */
	public function getApprovalUrl($plot, $user)
	{
		return $this->view->url('plot/approve-user', array(
			'user_id' => $user->getId(),
			'plot_id' => $plot->getId(),
			'role' => $user->getRole()
		));
	}

	/**
	 * @param $plot
	 * @param $user
	 * @return mixed
	 */
	public function getDenyUrl($plot, $user)
	{
		return $this->view->url('plot/deny-user', array(
			'user_id' => $user->getId(),
			'plot_id' => $plot->getId(),
			'role' => $user->getRole()
		));
	}
}