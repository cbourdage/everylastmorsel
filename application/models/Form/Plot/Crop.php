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

		$this->addElement('hidden', 'crop_id', array(
            'filters'    => array('Digits'),
            'required'   => true
        ));

		$this->addElement('text', 'date_planted', array(
            'filters'    => array('StringTrim'),
            'validators' => array('Date'),
            'required'   => true
        ));

		$this->addElement('text', 'variety', array(
			'label' => 'Crop Variety',
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(3, 128))
            ),
            'required'   => false
        ));

		$this->addElement('text', 'coverage', array(
			'label' => 'Coverage',
            'filters'    => array('StringTrim'),
            'validators' => array('Digits'),
            'required'   => true
        ));

		$this->addElement('select', 'coverage_unit', array(
			'label' => 'Units',
            'required'   => true,
			'multiOptions' => Elm_Model_Plot_Crop::$coverageUnits
        ));

		$this->addElement('radio', 'starting_type', array(
			'label' => 'Starting Type',
            'required'   => true,
			'multiOptions' => Elm_Model_Plot_Crop::$startingType
        ));

		/*
		$this->addElement('radio', 'conditions', array(
			'label' => 'Conditions',
            'required'   => true,
			'multiOptions' => Elm_Model_Plot_Crop::$conditions
        ));*/

        $this->prepareElements();
	}
}