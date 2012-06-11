<?php

class Elm_View_Helper_User extends Zend_View_Helper_Abstract
{
	private $_session = null;

	/**
	 * @return Elm_View_Helper_User
	 */
	public function User()
	{
		$this->_session = Elm::getSingleton('user/session');
		return $this;
	}

	public function getUnreadMessageCount($container = false)
	{
		$result = $this->_session->user->getUnreadMessageCount();
		if ($container === true) {
			$result = '<span class="message-count">' . $result . '</span>';
		}

		return $result;
	}

	public function canShowTips()
	{
		return $this->_session->user->getIsNew();
	}

	public function getProximity()
	{
		if ($location = Elm::getSingleton('session')->location) {
			return $location->getCity() . ', ' . $location->getState();
		}

		return null;
	}

	public function getImage($user)
	{
		if ($imageUrl = $user->getImage()) {
			if (file_exists(Elm::getBaseDir('http/media/user') . $imageUrl)) {
				return Elm::getBaseUrl('media/user') . $imageUrl;
			}
		}

		return Elm::getBaseUrl('media') . '/placeholder.jpg';
	}

	public function getFeed($user)
	{
		$feed = Elm::getModel('user_feed')->getItems($user, 10);
		return $feed;
	}
}