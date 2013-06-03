<?php


class Elm_Model_Form_Abstract extends Zend_Form
{
	/**
	 * @var array
	 */
	protected $_defaultDecorators = array(
		'ViewHelper',
		'Errors',
		array(array('data' => 'HtmlTag')),
		array('Label'),
		array('Row')
	);

	protected $_fileDecorators = array(
		'File',
		'Errors',
		array(array('data' => 'HtmlTag')),
		array('Label'),
        array('Row')
	);

	protected $_hiddenDecorators = array('ViewHelper');

	protected $_hiddenElements = array();

	protected $_visibleElements = array();

	public $buttonDecorators = array();

	public function __construct()
	{
        $this->addElementPrefixPath(
            'Elm_Model_Form_Filter',
            APPLICATION_PATH . '/models/Form/Filter/',
            Zend_Form_Element::FILTER
        );
        $this->addElementPrefixPath(
            'Elm_Model_Form_Validate',
            APPLICATION_PATH . '/models/Form/Validate/',
            Zend_Form_Element::VALIDATE
        );
		$this->addElementPrefixPath(
			'Elm_Model_Form_Decorator',
            APPLICATION_PATH . '/models/Form/Decorator/',
            Zend_Form_Element::DECORATOR
		);
	}

    /**
     * Sets form element decorators
     *
     * @return $this
     */
    public function prepareElements()
    {
        foreach ($this->getElements() as $element) {
            if ($element instanceof Zend_Form_Element_Hidden) {
                $element->setDecorators($this->_hiddenDecorators);
            } else {
                $element->setDecorators($this->_defaultDecorators);
            }
        }
        return $this;
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