<?php

class Elm_Model_InviteCode extends Colony_Model_Abstract
{
	public function _construct()
    {
        $this->_init('inviteCode');
    }

	/**
	 * @param int $count
	 * @return Elm_Model_InviteCode
	 */
	public function increment($count = 1)
	{
		$this->setTotalUsed($this->getTotalUsed() + $count);
		return $this;
	}
}