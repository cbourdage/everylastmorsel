<?php


class Elm_Model_Form_User_About extends Elm_Model_Form_Abstract
{
	public function __construct()
	{
		parent::__construct();

		$this->addElement('textarea', 'about', array(
            'rows' => 6,
            'cols' => 75,
            'required'   => false,
            'label'      => 'About Yourself'
        ));

        $session = Elm::getSingleton('user/session');
		foreach ($this->getElements() as $element) {
            $element->setDecorators($this->defaultDecorators);
            $element->setValue($session->user->getData($element->getName()));
		}
	}
}