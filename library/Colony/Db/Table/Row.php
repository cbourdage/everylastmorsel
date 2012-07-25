<?php


class Colony_Db_Table_Row extends Zend_Db_Table_Row_Abstract
{
	public function setCleanData($data)
	{
		$this->_cleanData = $data;
	}
}
