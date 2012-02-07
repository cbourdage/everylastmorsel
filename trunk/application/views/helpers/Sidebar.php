<?php

class Elm_View_Helper_Sidebar extends Zend_View_Helper_Abstract
{
	public function Sidebar()
	{
		//$this->_messages = Bootstrap::
		return $this;
	}

	public function render($clear = false)
	{
		$html = '';
		$session = Bootstrap::getSingleton('user/session');
		if ($allMessages = $session->getMessages($clear)) {
			$return = '<ul class="messages">';
			foreach ($allMessages as $type => $messages) {
				foreach ($messages as $message) {
					$return .= '<li class="' . $type . '">' . $message . '</li>';
				}
			}
			$return .= '</ul>';
		}
		return $html;
	}
}