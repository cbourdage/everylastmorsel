<?php


class Elm_Model_Form_Abstract extends Zend_Form
{
	public $defaultDecorators = array(
		'ViewHelper',
		'Errors',
		array(array('data' => 'HtmlTag')),
		array('Label'),
		array(array('row' => 'HtmlTag'), array('tag' => 'li'))
	);

	public $hiddenDecorators = array('ViewHelper');

	protected $_hiddenElements = array();

	protected $_visibleElements = array();

	public $buttonDecorators = array();

	public function __construct()
	{
        $this->addElementPrefixPath(
            'Elm_Model_Form_Validate',
            APPLICATION_PATH . '/models/form/validate/',
            Zend_Form_Element::VALIDATE
        );
	}

	/**
	 * Returns all non- type="hidden" form fields
	 *
	 * @return array
	 */
	public function getHiddenElements()
	{
		if (count($this->_hiddenElements) < 1) {
			foreach ($this->getElements() as $el) {
				if ($el instanceof Zend_Form_Element_Hidden) {
					$this->_hiddenElements[] = $el;
				}
			}
		}
		return $this->_hiddenElements;
	}

	/**
	 * Returns all non- type="hidden" form fields
	 *
	 * @return array
	 */
	public function getVisibleElements()
	{
		if (count($this->_visibleElements) < 1) {
			foreach ($this->getElements() as $el) {
				if (!$el instanceof Zend_Form_Element_Hidden) {
					$this->_visibleElements[] = $el;
				}
			}
		}
		return $this->_visibleElements;
	}
}