<?php


class Elm_Model_Form_User_Settings extends Elm_Model_Form_Abstract
{
	const VISIBILITY_PRIVATE = 'private';
	const VISIBILITY_PUBLIC = 'public';

	public function __construct()
	{
		parent::__construct();

        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', true, array(3, 128)),
                array('EmailAddress'),
                array('UniqueEmail', false, array(Elm::getModel('user'))),
            ),
            'required'   => true,
            'label'      => 'Email'
        ));

        $this->addElement('text', 'website', array(
            //'filters'    => array('StringTrim', 'StringToLower', 'Url'),
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', true, array(3, 128)),
            ),
            'required'   => false,
            'label'      => 'http://'
        ));
        $this->getElement('website')->addFilter('PregReplace', array('match' => '/http\:\/\//', 'replace' => ''));

		/*$this->addElement('checkbox', 'is_new', array(
            'required'   => false,
            'label'      => 'Show/Hide Tips'
        ));*/

		$this->addElement('radio', 'visibility', array(
            'required'   => true,
			'label' => 'Account Visibility',
			'multiOptions' => array(
				self::VISIBILITY_PUBLIC => 'Public',
				self::VISIBILITY_PRIVATE => 'Private'
			)
        ));

		$this->addElement('password', 'passwordCurrent', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(6, 128)),
                array('MatchingPassword', false, array(Elm::getModel('user'))),
            ),
            'required'   => false,
            'label'      => 'Current Password'
        ));

		$this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(6, 128))
            ),
            'required'   => false,
            'label'      => 'New Password'
        ));

        $this->addElement('password', 'passwordVerify', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
				'PasswordVerification'
			),
            'required'   => false,
            'label'      => 'Confirm Password'
        ));

		$session = Elm::getSingleton('user/session');
		foreach ($this->getElements() as $element) {
			$element->setDecorators($this->_defaultDecorators);
			$element->setValue($session->user->getData($element->getName()));
		}
	}
}