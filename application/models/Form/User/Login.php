<?php


class Elm_Model_Form_User_Login extends Elm_Model_Form_Abstract
{
	public function __construct()
	{
		parent::__construct();

		$this->setAction('/profile/login-post/');

        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', true, array(3, 128)),
                array('EmailAddress')
            ),
            'required'   => true,
            'label'      => 'Email',
			'id'		 => 'login-email-input'
        ));

        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(6, 128))
            ),
            'required'   => true,
            'label'      => 'Password',
			'id'		 => 'login-password-input'
        ));

        $this->prepareElements();
	}
}