<?php

class Elm_View_Helper_User extends Zend_View_Helper_Abstract
{
	private $_session = null;

	public function User()
	{
		$this->_session = Bootstrap::getSingleton('user/session');
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
		if ($location = Bootstrap::getSingleton('session')->location) {
			return $location->getCity() . ', ' . $location->getState();
		}

		return null;
	}

	public function getImage($user)
	{
		$imageUrl = ($user->getImage()) ? $user->getImage() : '/placeholder.gif';
		return Bootstrap::getBaseUrl('media/user') . $imageUrl;
	}

	public function getFeed($user)
	{
		$status = Bootstrap::getModel('user_feed')->getItems($user, 10);
		return $status;
	}
}