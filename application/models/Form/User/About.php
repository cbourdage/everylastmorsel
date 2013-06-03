<?php


class Elm_Model_Form_User_About extends Elm_Model_Form_Abstract
{
	public function __construct()
	{
		parent::__construct();

		$this->addElement('textarea', 'about', array(
            'label'      => 'About Yourself',
            'required'   => false,
            'rows' => 6,
            'cols' => 75
        ));

        $session = Elm::getSingleton('user/session');
		foreach ($this->getElements() as $element) {
            $element->setDecorators($this->_defaultDecorators);
            $element->setValue($session->user->getData($element->getName()));
		}
	}
}