<?php


class Elm_Model_Form_Communication_Abstract extends Elm_Model_Form_Abstract
{
	public static $subjects = array(
		'Subject 1' => 'Subject 1',
		'Subject 2' => 'Subject 2'
	);

	public function __construct()
	{
		parent::__construct();

		$this->setAction('/communication/send');
		$session = Elm::getSingleton('user/session');

		$this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(3, 128)),
            ),
            'required'   => true,
			'value'		 => $session->user->getName(),
            'label'      => 'Name'
        ));

        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', true, array(3, 128)),
                array('EmailAddress'),
            ),
            'required'   => true,
			'value'		 => $session->user->getEmail(),
            'label'      => 'Email'
        ));

		$this->addElement('select', 'subject', array(
            'required'   => true,
			'multiOptions' => self::$subjects,
			'label' => "What's this about?"
        ));

		$this->addElement('textarea', 'message', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(3, 128)),
            ),
            'required'   => true,
            'label'      => 'Message'
        ));

		foreach ($this->getElements() as $element) {
			$element->setDecorators($this->defaultDecorators);
		}
	}
}