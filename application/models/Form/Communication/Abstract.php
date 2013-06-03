<?php


class Elm_Model_Form_Communication_Abstract extends Elm_Model_Form_Abstract
{
	public static $subjects = array(
		'1' => 'Friendly Chitchat',
		'2' => 'Food Exchange'
	);

	public function __construct()
	{
		parent::__construct();

		$this->setAction('/communication/send');
		$session = Elm::getSingleton('user/session');

        if ($session->user) {
            $this->addElement('hidden', 'name', array(
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array('StringLength', true, array(3, 128)),
                ),
                'required'   => true,
                'value'		 => $session->user->getName(),
            ));

            $this->addElement('hidden', 'email', array(
                'filters'    => array('StringTrim', 'StringToLower'),
                'validators' => array(
                    array('StringLength', true, array(3, 128)),
                    array('EmailAddress'),
                ),
                'required'   => true,
                'value'		 => $session->user->getEmail(),
            ));
        } else {
            $this->addElement('text', 'name', array(
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array('StringLength', true, array(3, 128)),
                ),
                'required'   => true,
                'label'      => 'Name'
            ));

            $this->addElement('text', 'email', array(
                'filters'    => array('StringTrim', 'StringToLower'),
                'validators' => array(
                    array('StringLength', true, array(3, 128)),
                    array('EmailAddress'),
                ),
                'required'   => true,
                'label'      => 'Email'
            ));
        }
	}
}