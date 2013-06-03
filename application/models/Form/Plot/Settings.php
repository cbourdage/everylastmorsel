<?php


class Elm_Model_Form_Plot_Settings extends Elm_Model_Form_Abstract
{
    const VISIBILITY_PRIVATE = 'private';
    const VISIBILITY_PUBLIC = 'public';

    public function __construct()
    {
        parent::__construct();

        $this->addElement('radio', 'visibility', array(
            'label' => 'Plot Visibility',
            'required'   => true,
            'multiOptions' => array(
                Elm_Model_Form_Plot_Create::VISIBILITY_PUBLIC => 'Public',
                Elm_Model_Form_Plot_Create::VISIBILITY_PRIVATE => 'Private'
            )
        ));

        $this->prepareElements();
    }
}