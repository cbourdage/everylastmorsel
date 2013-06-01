<?php

/**
 * @TODO create an abstracted Plot Form Object
 */
class Elm_Model_Form_Plot_Edit extends Elm_Model_Form_Abstract
{

	public function __construct()
	{
		parent::__construct();

        $this->addElement('text', 'name', array(
            'label' => 'Plot Name',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(3, 128))
            )
        ));

		$this->addElement('textarea', 'about', array(
            'label' => 'About Location',
            'required' => false,
            'rows' => 6,
            'cols' => 75
        ));

		$this->addElement('radio', 'visibility', array(
            'label' => 'Plot Visibility',
            'required'   => true,
			'multiOptions' => array(
				Elm_Model_Form_Plot_Create::VISIBILITY_PUBLIC => 'Public',
				Elm_Model_Form_Plot_Create::VISIBILITY_PRIVATE => 'Private'
			)
        ));
		
        foreach ($this->getElements() as $element) {
			if ($element instanceof Zend_Form_Element_Hidden) {
				$element->setDecorators($this->hiddenDecorators);
			} else {
				$element->setDecorators($this->defaultDecorators);
			}
		}
	}
}