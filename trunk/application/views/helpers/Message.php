<?php

class Elm_View_Helper_Message extends Zend_View_Helper_Abstract
{
	public function Message()
	{
		//$this->_messages = Bootstrap::
		return $this;
	}

	public function render($clear = false)
	{
		$html = '';
		$session = Bootstrap::getSingleton('user/session');
		if ($allMessages = $session->getMessages($clear)) {
			$html = '<ul class="messages">';
			foreach ($allMessages as $type => $messages) {
				foreach ($messages as $message) {
					$html .= '<li class="' . $type . '">' . $message . '</li>';
				}
			}
			$html .= '</ul>';
		}
		return $html;
	}
}