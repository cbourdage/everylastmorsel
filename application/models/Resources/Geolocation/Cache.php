<?php

class Elm_Model_Resource_Geolocation_Cache extends Colony_Db_Table
{
    protected $_name = 'geolocation_cache';

	protected $_primary = 'entity_id';

    /**
     * Cleanup ip for save
     *
     * @param Colony_Model_Abstract $object
     * @return $this|Colony_Db_Table
     */
    protected function _beforeSave(Colony_Model_Abstract $object)
    {
        parent::_beforeSave($object);

        if ($object->isObjectNew()) {
            $object->setIpAddress(new Zend_Db_Expr("INET_ATON('{$object->getIpAddress()}')"));
        }
        return $this;
    }

}

