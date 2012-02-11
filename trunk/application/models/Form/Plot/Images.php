<?php



class Elm_Model_Form_Plot_Images extends Elm_Model_Form_Abstract
{
	public function __construct()
	{
		parent::__construct();

		//Bootstrap::log(Bootstrap::getBaseDir('http/media/plots'));

		$this->setAttrib('enctype', 'multipart/form-data');

		// Images input
		$element = new Zend_Form_Element_File('image');
		$element->setLabel('Image:')
			//->setDestination(Bootstrap::getBaseDir('http/media/plots') . '/upload')
			->addValidator('Size', false, 102400)	// limit to 100K
			->addValidator('Extension', false, 'jpg,png,gif,jpeg'); // only JPEG, PNG, and GIFs
		//$element->setMultiFile(3);
		$element->setDecorators($this->fileDecorators);
		$this->addElement($element, 'image');

		// Image titile
        $this->addElement('text', 'title', array(
            'filters' => array('StringTrim'),
			'decorators' => $this->defaultDecorators,
            'validators' => array(
                array('StringLength', true, array(0, 255))
            ),
            'label' => 'Title'
        ));
	}
}
