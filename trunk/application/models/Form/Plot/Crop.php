<?php


class Elm_Model_Form_Plot_Crop extends Elm_Model_Form_Abstract
{
	public function __construct()
	{
		parent::__construct();

		$this->addElement('hidden', 'user_id', array(
            'filters'    => array('Digits'),
            'required'   => true
        ));

        $this->addElement('hidden', 'plot_id', array(
            'filters'    => array('Digits'),
            'required'   => true
        ));

		$this->addElement('text', 'date_planted', array(
            'filters'    => array('StringTrim'),
            'validators' => array('Date'),
            'required'   => true
        ));

		$this->addElement('text', 'quantity', array(
			'label' => 'Number of Plants',
            'filters'    => array('StringTrim'),
            'validators' => array('Digits'),
            'required'   => true
        ));

		$this->addElement('text', 'coverage', array(
			'label' => 'Coverage',
            'filters'    => array('StringTrim'),
            'validators' => array('Digits'),
            'required'   => true
        ));

		$this->addElement('radio', 'conditions', array(
			'label' => 'Conditions',
            'required'   => true,
			'multiOptions' => Elm_Model_Plot_Crop::$conditions
        ));

		$this->addElement('radio', 'starting_type', array(
			'label' => 'Starting Type',
            'required'   => true,
			'multiOptions' => Elm_Model_Plot_Crop::$startingType
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