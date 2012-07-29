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
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(3, 128))
            ),
            'required' => true,
            'label' => 'Plot Name',
        ));

		$this->addElement('textarea', 'about', array(
            'required'   => false,
            'label'      => 'About Location'
        ));

		$this->addElement('radio', 'visibility', array(
            'required'   => true,
			'label' => 'Plot Visibility',
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