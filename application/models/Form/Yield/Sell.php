<?php


class Elm_Model_Form_Yield_Sell extends Elm_Model_Form_Abstract
{
	public function __construct()
	{
		parent::__construct();

		$this->setAction('/yields/sell-yield-post');

		$this->addElement('hidden', 'yield_id', array(
            'filters'    => array('Digits'),
            'required'   => true
        ));

		$this->addElement('text', 'quantity', array(
			'label' => 'Quantity',
            'required'   => true,
        ));

		$this->addElement('text', 'price', array(
			'label' => 'Price',
            'required'   => true,
        ));

        $this->prepareElements();
	}
}