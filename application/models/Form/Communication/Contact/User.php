<?php


class Elm_Model_Form_Communication_Contact_User extends Elm_Model_Form_Communication_Abstract
{
    const TYPE = 'User';

    public function __construct()
    {
        parent::__construct();

        $this->addElement('hidden', 'type', array(
            'value' => self::TYPE
        ));

        $this->addElement('select', 'subject', array(
            'required'   => true,
            'multiOptions' => self::$subjects,
            'id' => 'user-subject',
            'label' => "What's this about?"
        ));

        $this->addElement('textarea', 'message', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'id' => 'user-message',
            'label'      => 'Message'
        ));

        $this->prepareElements();
    }
}