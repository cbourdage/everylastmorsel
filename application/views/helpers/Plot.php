<?php

class Elm_View_Helper_Plot extends Zend_View_Helper_Abstract
{
	private $_session = null;

	/**
	 * @return Elm_View_Helper_Plot
	 */
	public function Plot()
	{
		$this->_session = Elm::getSingleton('user/session');
		return $this;
	}

	public function getPlot()
	{
		if (Zend_Registry::isRegistered('current_plot')) {
			return Zend_Registry::get('current_plot');
		}
		return null;
	}

	public function getImage($image)
	{
		if (file_exists(Elm::getBaseDir('http/media/plot') . $image)) {
			return Elm::getBaseUrl('media/plot') . $image;
		}

		return Elm::getBaseUrl('media') . '/placeholder.jpg';
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

	/**
	 * @param $users
	 * @return mixed
	 */
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

	/**
	 * Checks if any crops are for sale
	 *
	 * @param $plot
	 * @return bool
	 */
	public function hasCropsForSale($plot)
	{
		foreach ($plot->getCrops() as $pCrop) {
			if ($pCrop->getIsForSale()) {
				return true;
			}
		}
		return false;
	}
}