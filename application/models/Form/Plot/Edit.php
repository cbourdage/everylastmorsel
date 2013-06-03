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

        $this->prepareElements();
	}
}