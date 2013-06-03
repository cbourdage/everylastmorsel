<?php


class Elm_Model_Form_Communication_Contact_Owner extends Elm_Model_Form_Communication_Abstract
{
    const TYPE = 'Owner';

    public function __construct()
    {
        parent::__construct();

        $this->addElement('hidden', 'type', array(
            'value' => self::TYPE
        ));

        $this->addElement('text', 'subject', array(
            'required'   => true,
            'id' => 'owner-subject',
            'label' => "Subject"
        ));

        $this->addElement('textarea', 'message', array(
            'filters' => array('StringTrim'),
            /*'validators' => array(
                array('StringLength', true, array(3, 128)),
            ),*/
            'required' => true,
            'id' => 'owner-message',
            'label' => 'Message'
        ));

        $this->prepareElements();
    }
}