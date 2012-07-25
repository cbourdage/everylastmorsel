<?php

class Elm_Model_Resource_InviteCode extends Colony_Db_Table
{
    protected $_name = 'invite_codes';

	protected $_primary = 'code';

	protected function _afterSave($object)
	{
		parent::_afterSave($object);

		if ($object->getIsAvalable() && (int) $object->getTotalAvailable() >= (int) $object->getTotalUsed()) {
			$object->setIsAvailable(false)->save();
		}

		return $this;
	}
	/**
	 * Checks if the provided code is available for use
	 *
	 * @param $code
	 * @return bool
	 */
	public function isAvailable($code)
	{
		$exists = $this->getAdapter()->fetchOne("SELECT 1 FROM " . $this->_name . " is_active = 1 AND code = ?", $code);
		return !empty($exists);
	}
}

