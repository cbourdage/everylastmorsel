<?php


class Elm_Model_Form_Yield_Add extends Elm_Model_Form_Abstract
{
	public function __construct()
	{
		parent::__construct();

		$this->addElement('hidden', 'plot_crop_id', array(
            'filters'    => array('Digits'),
            'required'   => true
        ));

		$this->addElement('text', 'quantity', array(
			'label' => 'Yield',
            'required'   => true,
        ));

		$this->addElement('text', 'date_picked', array(
            'filters'    => array('StringTrim'),
            'validators' => array('Date'),
            'required'   => true
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