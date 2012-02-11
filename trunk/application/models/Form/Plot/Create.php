<?php


class Elm_Model_Form_Plot_Create extends Elm_Model_Form_Abstract
{
	public function __construct()
	{
		parent::__construct();

        $this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(3, 128))
            ),
            'required'   => true,
            'label'      => 'Name',
        ));

		$this->addElement('hidden', 'latitude', array(
            'filters'    => array('StringTrim'),
            'validators' => array('Float'),
            'required'   => true,
			'value'      => Bootstrap::getSingleton('user/session')->plot['latitude']
        ));

        $this->addElement('hidden', 'longitude', array(
            'filters'    => array('StringTrim'),
            'validators' => array('Float'),
            'required'   => true,
			'value'      => Bootstrap::getSingleton('user/session')->plot['longitude']
        ));

		// @TODO Auto-populate zipcode based on lat & long
		$this->addElement('text', 'zipcode', array(
            'filters'    => array('StringTrim'),
            'validators' => array('PostCode'),
            'required'   => true,
            'label'      => 'Zipcode'
        ));

		if (Bootstrap::getSingleton('user/session')->isLoggedIn()) {
			$this->addElement('hidden', 'user_id', array(
				'required'   => true,
				'value'      => Bootstrap::getSingleton('user/session')->id
			));
		}
		
        foreach ($this->getElements() as $element) {
			if ($element instanceof Zend_Form_Element_Hidden) {
				$element->setDecorators($this->hiddenDecorators);
			} else {
				$element->setDecorators($this->defaultDecorators);
			}
		}
	}
}