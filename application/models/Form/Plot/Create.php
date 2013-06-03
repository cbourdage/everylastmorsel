<?php


class Elm_Model_Form_Plot_Create extends Elm_Model_Form_Abstract
{
	const VISIBILITY_PRIVATE = 'private';
	const VISIBILITY_PUBLIC = 'public';

	public function __construct()
	{
		parent::__construct();

		if (Elm::getSingleton('user/session')->isLoggedIn()) {
			$this->addElement('hidden', 'user_id', array(
				'required'   => true,
				'value'      => Elm::getSingleton('user/session')->id
			));
		}

        $this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(3, 128))
            ),
            'required'   => true,
            'label'      => 'Plot Name',
        ));

		$this->addElement('hidden', 'latitude', array(
            'filters'    => array('StringTrim'),
            'validators' => array('Float'),
            'required'   => true,
			'value'      => Elm::getSingleton('user/session')->plot['latitude']
        ));

        $this->addElement('hidden', 'longitude', array(
            'filters'    => array('StringTrim'),
            'validators' => array('Float'),
            'required'   => true,
			'value'      => Elm::getSingleton('user/session')->plot['longitude']
        ));

		$this->addElement('text', 'address', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(3, 128))
            ),
            'required'   => false,
            'label'      => 'Address'
        ));

		$this->addElement('text', 'city', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(2, 32))
            ),
            'required'   => true,
            'label'      => 'City'
        ));

		$this->addElement('text', 'state', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(2, 32))
            ),
			'value'		 => 'IL',
            'required'   => true,
            'label'      => 'State'
        ));

		$this->addElement('text', 'zipcode', array(
            'filters'    => array('StringTrim'),
            'validators' => array('PostCode'),
            'required'   => true,
            'label'      => 'Zipcode'
        ));

		$this->addElement('radio', 'visibility', array(
            'required'   => true,
			'label' => 'Plot Visibility',
			'multiOptions' => array(
				self::VISIBILITY_PUBLIC => 'Public',
				self::VISIBILITY_PRIVATE => 'Private'
			)
        ));

        $this->prepareElements();
	}
}