<?php



class Elm_Model_Form_Plot_Images extends Elm_Model_Form_Abstract
{
	public function __construct()
	{
		parent::__construct();

		//Elm::log(Elm::getBaseDir('http/media/plots'));
		$this->setAttrib('enctype', 'multipart/form-data');
		$this->setIsArray(true);
		// Images input
		$element = new Zend_Form_Element_File('image');
		$element->setLabel('Image')
			//->setDestination(Elm::getBaseDir('http/media/plots') . '/upload')
			->addValidator('Size', false, 102400)	// limit to 100K
			->addValidator('Extension', false, 'jpg,png,gif,jpeg'); // only JPEG, PNG, and GIFs
		$element->setDecorators($this->fileDecorators);
		$element->setBelongsTo('image');
		//$element->setMultiFile(1);
		$this->addElement($element, 'image');

		// Image titile
        $this->addElement('text', 'caption', array(
			'label' => 'Title/Caption',
			'decorators' => $this->_defaultDecorators,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(0, 255))
            )
        ));
		$element = $this->getElement('caption');
		$element->setBelongsTo('caption');
	}
}
