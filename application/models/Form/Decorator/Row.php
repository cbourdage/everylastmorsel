<?php

class Elm_Model_Form_Decorator_Row extends Zend_Form_Decorator_Abstract
{
    protected $_controls = array('Zend_Form_Element_Radio', 'Zend_Form_Element_Checkbox', 'Zend_Form_Element_Select', 'Zend_Form_Element_Multiselect');

    public function render($content)
    {
        return '<li class="' . strtolower($this->getElement()->getName()) . (in_array($this->getElement()->getType(), $this->_controls) ? ' control' : '') . '">'
            . $content
            . '</li>';
    }
}
