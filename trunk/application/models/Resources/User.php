<?php

class Elm_Model_Resource_User extends Colony_Db_Table
{
    protected $_name = 'user';

	protected $_primary = 'user_id';

	public static $gardenerTypes = array(
		'casual' => 'Casual',
		'farmer' => 'Farmer',
		'community' => 'Community'
	);

    /**
     * Check customer scope, email and confirmation key before saving
     *
     * @param Colony_Model_Abstract $object
     * @return Elm_Model_Resource_User
     * @throws Colony_Exception
     */
    protected function _beforeSave(Colony_Model_Abstract $object)
    {
        parent::_beforeSave($object);
        if (!$object->getEmail()) {
            Elm::throwException('User email is required.');
        }
        return $this;
    }

    /**
     * Save customer addresses and set default addresses in attributes backend
     *
     * @param   Colony_Model_Abstract $object
     * @return  Colony_Db_Table
     */
    protected function _afterSave(Colony_Model_Abstract $object)
    {
        return parent::_afterSave($object);
    }

	/**
	 * @param $object
	 * @return Elm_Model_Resource_User
	 */
	protected function _afterLoad($object)
	{
		parent::_afterLoad($object);

		if ($object->getId()) {
			$select = $this->getDefaultAdapter()->select()
				->from(Elm_Model_Resource_Plot::RELATIONSHIP_TABLE)
				->where('user_id = ?', $object->getId())
				->where('is_approved = 1');
			if ($rows = $this->getDefaultAdapter()->fetchAll($select)) {
				$plots = array();
				foreach ($rows as $row) {
					$plots[$row->plot_id][] = new Colony_Object(Elm::toArray($row)); //Elm::getModel('plot')->load($row->plot_id, false);
				}
				$object->setAssociatedPlots($plots);
			} else {
				$object->setAssociatedPlots(null);
			}
		}

		return $this;
	}

	/**
     * Load customer by email
     *
     * @param Elm_Model_User $user
     * @param string $email
     * @return Elm_Model_Resource_User
     * @throws Colony_Exception
     */
    public function loadByEmail(Elm_Model_User $user, $email)
    {
		$row = $this->fetchRow(Zend_Db_Table::getDefaultAdapter()->quoteInto('email = ?', $email));
        if ($row !== null) {
            $this->load($user, $row->user_id);
        } else {
            $user->setData(array());
        }
        return $this;
    }

	/**
     * Load customer by email
     *
     * @param Elm_Model_User $user
     * @param string $alias
     * @return Elm_Model_Resource_User
     * @throws Colony_Exception
     */
    public function loadByAlias(Elm_Model_User $user, $alias)
    {
		$row = $this->fetchRow(Zend_Db_Table::getDefaultAdapter()->quoteInto('alias = ?', $alias));
        if ($row !== null) {
            $this->load($user, $row->user_id);
        } else {
            $user->setData(array());
        }
        return $this;
    }

	/**
     * Check user by id
     *
     * @param int $userId
     * @return bool
     */
    public function checkUserId($userId)
    {
        return $select = $this->find($userId)->current();
    }

	/**
	 * @param Elm_Model_User $user
	 * @param $key
	 * @return bool
	 */
	public function checkConfirmationKey(Elm_Model_User $user, $key)
	{
		$row = $this->fetchRow(Zend_Db_Table::getDefaultAdapter()->quoteInto('confirmation_key = ?', $key));
		if ($row !== null) {
            return $this->load($user, $row->user_id);
        } else {
            return false;
        }
	}

    /**
     * Check whether there are email duplicates of customers in global scope
     *
     * @return bool
     */
    public function findEmailDuplicates()
    {
        $lookup = $this->getDefaultAdapter()->fetchRow("
			SELECT email, COUNT(*) AS `qty`
            FROM `user`
            GROUP BY 1
            ORDER BY 2 DESC
            LIMIT 1
        ");

        if (empty($lookup)) {
            return false;
        }
        return $lookup['qty'] > 1;
    }
}

