<?php


class Elm_Model_Plot_Form_Create extends Zend_Form
{
	public function __construct()
	{
		// add path to custom validators
        $this->addElementPrefixPath(
            'Elm_Model_Form_Validate',
            APPLICATION_PATH . '/models/form/validate/',
            Zend_Form_Element::VALIDATE
        );

		// @TODO Create unique email check
        $this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(3, 128))
            ),
            'required'   => true,
            'label'      => 'Name',
        ));

		$this->addElement('text', 'latitude', array(
            'filters'    => array('StringTrim'),
            'validators' => array('Float'),
            'required'   => true,
            'label'      => 'Latitude',
        ));

        $this->addElement('text', 'longitude', array(
            'filters'    => array('StringTrim'),
            'validators' => array('Float'),
            'required'   => true,
            'label'      => 'Longitude',
        ));

		// @TODO Auto-populate zipcode based on lat & long
		$this->addElement('text', 'zipcode', array(
            'filters'    => array('StringTrim'),
            'validators' => array('PostCode'),
            'required'   => false,
            'label'      => 'Zipcode',
        ));

        $this->addElement('submit', 'submit', array(
            'required' => false,
            'ignore'   => true,
            'decorators' => array('ViewHelper',array('HtmlTag', array('tag' => 'dd', 'id' => 'form-submit')))
        ));
	}
}