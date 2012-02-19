<?php

/**
 * Abstract model class
 * 
 */
abstract class Colony_Model_Abstract extends Colony_Object
{
    /**
     * Name of the resource model
     *
     * @var string
     */
    protected $_resourceName;

    /**
     * Resource model instance
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_resource;

    /**
     * Flag which allow detect object state: is it new object (without id) or existing one (with id)
     *
     * @var bool
     */
    protected $_isObjectNew = null;

    /**
     * Standard model initialization
     *
     * @param string $resourceModel
     */
    protected function _init($resourceModel)
    {
		$this->_resourceName = $resourceModel;
    }

    /**
     * Get resource instance
     *
     * @return Zend_Db_Table_Abstract
     */
    protected function _getResource()
    {
        if (empty($this->_resourceName)) {
            Bootstrap::throwException('Resource is not set.');
        }

        return Bootstrap::getResourceSingleton($this->_resourceName);
    }

	/**
     * Retrieve model resource
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getResource()
    {
        return $this->_getResource();
    }

    /**
     * Retrieve identifier field name for model
     *
     * @return string
     */
    public function getIdFieldName()
    {
        if (!($fieldName = parent::getIdFieldName())) {
            $fieldName = $this->_getResource()->info(Zend_Db_Table_Abstract::PRIMARY);
			$fieldName = is_array($fieldName) ? array_shift($fieldName) : $fieldName;
            $this->setIdFieldName($fieldName);
        }
        return $fieldName;
    }

    /**
     * Retrieve model object identifier
     *
     * @return mixed
     */
    public function getId()
    {
        if ($fieldName = $this->getIdFieldName()) {
            return $this->_getData($fieldName);
        } else {
            return $this->_getData('id');
        }
    }

    /**
     * Declare model object identifier value
     *
     * @param   mixed $id
     * @return  Colony_Model_Abstract
     */
    public function setId($id)
    {
        if ($fieldName = $this->getIdFieldName()) {
            $this->setData($fieldName, $id);
        } else {
            $this->setData('id', $id);
        }
        return $this;
    }

    /**
     * Retrieve model resource name
     *
     * @return string
     */
    public function getResourceName()
    {
        return $this->_resourceName;
    }

    /**
     * Load object data
     *
     * @param   integer $id
     * @param   boolean $additional
     * @return  Colony_Model_Abstract
     */
    public function load($id, $additional=true)
    {
        $this->_beforeLoad();
        $this->_getResource()->load($this, $id, $additional);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }

    /**
     * Processing object before load data
     *
     * @return Colony_Model_Abstract
     */
    protected function _beforeLoad()
    {
        return $this;
    }

    /**
     * Processing object after load data
     *
     * @return Colony_Model_Abstract
     */
    protected function _afterLoad()
    {
        return $this;
    }

    /**
     * Check whether model has changed data.
     * Can be overloaded in child classes to perform advanced check whether model needs to be saved
     * e.g. usign resouceModel->hasDataChanged() or any other technique
     *
     * @return boolean
     */
    protected function _hasModelChanged()
    {
        return $this->hasDataChanges();
    }

    /**
     * Save object data
     *
     * @return Colony_Model_Abstract
     */
    public function save()
    {
        /**
         * Direct deleted items to delete method
         */
        if ($this->isDeleted()) {
            return $this->delete();
        }

        if (!$this->_hasModelChanged()) {
            return $this;
        }

        //$this->_getResource()->beginTransaction();
		//$dbAdapter = Zend_Registry::get('db');
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		$dbAdapter->beginTransaction();
        $dataCommited = false;
        try {
            $this->_beforeSave();
			$this->_getResource()->save($this);
			$this->_afterSave();
            $dbAdapter->commit();
            $this->_hasDataChanges = false;
            $dataCommited = true;
        } catch (Exception $e) {
            $dbAdapter->rollBack();
            $this->_hasDataChanges = true;
            throw $e;
        }
        return $this;
    }

    /**
     * Check object state (true - if it is object without id on object just created)
     * This method can help detect if object just created in _afterSave method
     * problem is what in after save onject has id and we can't detect what object was
     * created in this transaction
     *
     * @param bool $flag
     * @return bool
     */
    public function isObjectNew($flag=null)
    {
        if ($flag !== null) {
            $this->_isObjectNew = $flag;
        }
        if ($this->_isObjectNew !== null) {
            return $this->_isObjectNew;
        }
        return !(bool)$this->getId();
    }

    /**
     * Processing object before save data
     *
     * @return Colony_Model_Abstract
     */
    protected function _beforeSave()
    {
        return $this;
    }

    /**
     * Processing object after save data
     *
     * @return Colony_Model_Abstract
     */
    protected function _afterSave()
    {
        return $this;
    }

    /**
     * Delete object from database
     *
     * @return Colony_Model_Abstract
     */
    public function delete()
    {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
		$dbAdapter->beginTransaction();
        try {
            $this->_getResource()->delete($this);
            $dbAdapter->commit();
        }
        catch (Exception $e){
            $dbAdapter->rollBack();
            throw $e;
        }
        return $this;
    }
}
