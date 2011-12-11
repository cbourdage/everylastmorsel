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

		// @TODO Create unique email check
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', true, array(3, 128)),
                array('EmailAddress')
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

        $this->addElement('submit', 'submit', array(
            'required' => false,
            'ignore'   => true,
            'decorators' => array('ViewHelper',array('HtmlTag', array('tag' => 'dd', 'id' => 'form-submit')))
        ));
	}
}