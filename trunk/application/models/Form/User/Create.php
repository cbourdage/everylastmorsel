<?php


class Elm_Model_Form_User_Create extends Elm_Model_Form_Abstract
{
	public function __construct()
	{
		parent::__construct();

		$this->setAction('/profile/create/');

		if (!Elm::getAppConfig('public')) {
			$this->addElement('text', 'invite_code', array(
				'filters'    => array('StringTrim'),
				'validators' => array(
					array('StringLength', true, array(3, 24)),
					array('InviteCode', true),
				),
				'required'   => true,
				'label'      => 'Invite Code'
			));
		}

		$this->addElement('text', 'firstname', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                //'Alpha',
                array('StringLength', true, array(3, 128))
            ),
            'required'   => true,
            'label'      => 'First Name'
        ));

        $this->addElement('text', 'lastname', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                //'Alpha',
                array('StringLength', true, array(3, 128))
            ),
            'required'   => true,
            'label'      => 'Last Name'
        ));

        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', true, array(3, 128)),
                array('EmailAddress'),
                array('UniqueEmail', true, array(Elm::getModel('user'))),
            ),
            'required'   => true,
            'label'      => 'Email'
        ));

		$this->addElement('text', 'city', array(
            'filters'    => array('StringTrim'),
            'validators' => array(),
            'required'   => false,
            'label'      => 'City'
        ));

		$this->addElement('text', 'state', array(
            'filters'    => array('StringTrim'),
            'validators' => array(),
            'required'   => false,
            'label'      => 'State',
			'value' => 'Illinois'
        ));

/*		$this->addElement('text', 'zipcode', array(
            'filters'    => array('StringTrim'),
            'validators' => array(),
            'required'   => false,
            'label'      => 'State',
			'value' => 'Illinois'
        ));*/

        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(6, 128))
            ),
            'required'   => true,
            'label'      => 'Password'
        ));

        $this->addElement('password', 'passwordVerify', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
               'PasswordVerification',
            ),
            'required'   => true,
            'label'      => 'Confirm Password'
        ));


		foreach ($this->getElements() as $element) {
			$element->setDecorators($this->defaultDecorators);
		}
	}
}