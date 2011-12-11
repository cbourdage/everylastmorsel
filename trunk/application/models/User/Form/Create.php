<?php


class Elm_Model_User_Form_Create extends Zend_Form
{
	public function __construct()
	{
		// add path to custom validators
        $this->addElementPrefixPath(
            'Elm_Model_Form_Validate',
            APPLICATION_PATH . '/models/form/validate/',
            Zend_Form_Element::VALIDATE
        );


		$this->addElement('text', 'firstname', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alpha',
                array('StringLength', true, array(3, 128))
            ),
            'required'   => true,
            'label'      => 'First Name',
        ));

        $this->addElement('text', 'lastname', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alpha',
                array('StringLength', true, array(3, 128))
            ),
            'required'   => true,
            'label'      => 'Last Name',
        ));

        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', true, array(3, 128)),
                array('EmailAddress'),
                array('UniqueEmail', false, array(Bootstrap::getModel('user'))),
            ),
            'required'   => true,
            'label'      => 'Email',
        ));

        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(6, 128))
            ),
            'required'   => true,
            'label'      => 'Password',
        ));

        $this->addElement('password', 'passwordVerify', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
               'PasswordVerification',
            ),
            'required'   => true,
            'label'      => 'Confirm Password',
        ));

		// @TODO Auto-populate zipcode based on ip address
		$this->addElement('text', 'location', array(
            'filters'    => array('StringTrim'),
            'validators' => array(),
            'required'   => false,
            'label'      => 'Location',
        ));

        $this->addElement('submit', 'submit', array(
            'required' => false,
            'ignore'   => true,
            'decorators' => array('ViewHelper',array('HtmlTag', array('tag' => 'dd', 'id' => 'form-submit')))
        ));
	}
}