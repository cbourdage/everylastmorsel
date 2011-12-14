<?php


class Elm_Model_User extends Colony_Model_Abstract
{
	const EXCEPTION_EMAIL_NOT_CONFIRMED       = 1;
    const EXCEPTION_INVALID_EMAIL_OR_PASSWORD = 2;
    const EXCEPTION_EMAIL_EXISTS              = 3;
	
	public function _construct()
    {
        $this->_init('user');
    }
	
	/**
     * Authenticate customer
     *
     * @param  string $login
     * @param  string $password
     * @return true
     * @throws Exception
     */
    public function authenticate($login, $password)
    {
        $this->loadByEmail($login);
        if (!$this->validatePassword($password)) {
            throw new Colony_Exception('Invalid login or password', self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD);
        }
		else {
			// @TODO set last_login_at field in database
		}

        return true;
    }

    /**
     * Load customer by email
     *
     * @param   string $email
     * @return  Elm_Model_User
     */
    public function loadByEmail($email)
    {
        $this->_getResource()->loadByEmail($this, $email);
        return $this;
    }

	/**
     * Load customer by email
     *
     * @param   string $alias
     * @return  Elm_Model_User
     */
    public function loadByAlias($alias)
    {
        $this->_getResource()->loadByAlias($this, $alias);
        return $this;
    }


    /**
     * Processing object before save data
     *
	 * @TODO setup a 'is_new' variable for new accounts - must expire on third login or if user disables tooltips/guide information
	 *
     * @return Colony_Model_Abstract
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
		$this->setAlias(substr($this->getEmail(), 0, strpos($this->getEmail(), '@')));
        return $this;
    }

    /**
     * Change customer password
     *
     * @param   string $newPassword
     * @return  this
     */
    public function changePassword($newPassword)
    {
        $this->_getResource()->changePassword($this, $newPassword);
        return $this;
    }

    /**
     * Get full customer name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }

    /**
     * Set plain and hashed password
     *
     * @param string $password
     * @return Elm_Model_User
     */
    public function setPassword($password)
    {
        $this->setData('password', $password);
        $this->setPasswordHash($this->hashPassword($password));
        return $this;
    }

    /**
     * Hash customer password
     *
     * @param   string $password
     * @param   string $salt
     * @return  string
     */
    public function hashPassword($password, $salt=null)
    {
        return Colony_Hash::getHash($password, !is_null($salt) ? $salt : 2);
    }

    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length=6)
    {
        return Colony_Hash::getRandomString($length);
    }

    /**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        if (!($hash = $this->getPasswordHash())) {
            return false;
        }
        return Colony_Hash::validateHash($password, $hash);
    }

    /**
     * Send email with new account specific information
     *
	 * @TODO send new account email
	 *
	 * @param string $backUrl
     * @return Elm_Model_User
     */
    public function sendNewAccountEmail($backUrl = '')
    {
        //http://stackoverflow.com/questions/1218191/how-can-i-make-email-template-in-zend-framework
		Bootstrap::log(__METHOD__);
        return $this;
    }

    public function getRandomConfirmationKey()
    {
        return md5(uniqid());
    }

    /**
     * Send email with new customer password
     *
     * @return Elm_Model_User
     */
    public function sendPasswordReminderEmail()
    {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $storeId = $this->getStoreId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        Mage::getModel('core/email_template')
            ->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
            ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_TEMPLATE, $storeId),
                Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_IDENTITY, $storeId),
                $this->getEmail(),
                $this->getName(),
                array('customer'=>$this)
            );

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Validate customer attribute values.
     * For existing customer password + confirmation will be validated only when password is set (i.e. its change is requested)
     *
     * @return bool
     */
    public function validate()
    {
        $errors = array();
        $customerHelper = Mage::helper('customer');
        if (!Zend_Validate::is( trim($this->getFirstname()) , 'NotEmpty')) {
            $errors[] = $customerHelper->__('The first name cannot be empty.');
        }

        if (!Zend_Validate::is( trim($this->getLastname()) , 'NotEmpty')) {
            $errors[] = $customerHelper->__('The last name cannot be empty.');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = $customerHelper->__('Invalid email address "%s".', $this->getEmail());
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Zend_Validate::is($password , 'NotEmpty')) {
            $errors[] = $customerHelper->__('The password cannot be empty.');
        }
        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(6))) {
            $errors[] = $customerHelper->__('The minimum password length is %s', 6);
        }
        $confirmation = $this->getConfirmation();
        if ($password != $confirmation) {
            $errors[] = $customerHelper->__('Please make sure your passwords match.');
        }

        $entityType = Mage::getSingleton('eav/config')->getEntityType('customer');
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'dob');
        if ($attribute->getIsRequired() && '' == trim($this->getDob())) {
            $errors[] = $customerHelper->__('The Date of Birth is required.');
        }
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'taxvat');
        if ($attribute->getIsRequired() && '' == trim($this->getTaxvat())) {
            $errors[] = $customerHelper->__('The TAX/VAT number is required.');
        }
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'gender');
        if ($attribute->getIsRequired() && '' == trim($this->getGender())) {
            $errors[] = $customerHelper->__('Gender is required.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    function addError($error)
    {
        $this->_errors[] = $error;
    }

    function getErrors()
    {
        return $this->_errors;
    }

    function resetErrors()
    {
        $this->_errors = array();
    }

    function printError($error, $line = null)
    {
        if ($error == null) return false;
        $img = 'error_msg_icon.gif';
        $liStyle = 'background-color:#FDD; ';
        echo '<li style="'.$liStyle.'">';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/'.$img).'" class="v-middle"/>';
        echo $error;
        if ($line) {
            echo '<small>, Line: <b>'.$line.'</b></small>';
        }
        echo "</li>";
    }

    /**
     * Reset all model data
     *
     * @return Elm_Model_User
     */
    public function reset()
    {
        $this->setData(array());
        $this->setOrigData();
        return $this;
    }
}