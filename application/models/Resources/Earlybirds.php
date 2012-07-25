<?php

class Elm_Model_Resource_Earlybirds extends Colony_Db_Table
{
    protected $_name = 'early_signups';

	protected $_primary = 'entity_id';

	/**
	 * Loads newsletter data based on email
	 *
	 * @param Elm_Model_Newsletter $obj
	 * @param $email
	 * @return Elm_Model_Resource_Newsletter
	 */
	public function loadByEmail(Elm_Model_Newsletter $obj, $email)
	{
		$row = $this->fetchRow($this->getDefaultAdapter()->quoteInto('email = ?', $email));
        if ($row !== null) {
            $this->load($obj, $row->newsletter_id);
        } else {
            $obj->setData(array());
        }
        return $this;
	}

	/**
	 * Looks up an email address
	 *
	 * @param $email
	 * @return Elm_Model_Resource_Newsletter
	 */
	public function emailLookup($email)
	{
		$row = $this->fetchRow($this->getDefaultAdapter()->quoteInto('email = ?', $email));
        if ($row !== null) {
			return true;
        }
        return false;
	}
}

