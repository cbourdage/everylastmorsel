<?php


class Elm_Model_Form_User_Info extends Elm_Model_Form_Abstract
{
	public function __construct()
	{
		parent::__construct();

		$this->addElement('radio', 'gardener_type', array(
            'required'   => true,
			'label' => 'Type of Gardener',
			'multiOptions' => Elm_Model_Resource_User::$gardenerTypes
        ));

		$this->addElement('textarea', 'about', array(
            'required'   => false,
            'label'      => 'About Yourself'
        ));

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

		// Image input
		$element = new Zend_Form_Element_File('image');
		$element->setLabel('Upload a profile picture')
			//->setDestination(Elm::getBaseDir('http/media/plots') . '/upload')
			->addValidator('Size', false, 102400 * 4)	// limit to 100K
			->addValidator('Extension', false, 'jpg,png,gif,jpeg'); // only JPEG, PNG, and GIFs
		$element->setDecorators($this->fileDecorators);
		$element->setBelongsTo('image');
		$this->addElement($element, 'image');
		$this->setAttrib('enctype', 'multipart/form-data');


		$session = Elm::getSingleton('user/session');
		foreach ($this->getElements() as $element) {
			if ($element->getType() != 'Zend_Form_Element_File') {
				$element->setDecorators($this->defaultDecorators);
				$element->setValue($session->user->getData($element->getName()));
			}
		}
	}
}