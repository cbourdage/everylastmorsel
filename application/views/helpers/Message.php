<?php

class Elm_View_Helper_Message extends Zend_View_Helper_Abstract
{
	public function Message()
	{
		//$this->_messages = Elm::
		return $this;
	}

	public function render($clear = true)
	{
		$html = '';
		$session = Elm::getSingleton('user/session');
		if ($allMessages = $session->getMessages($clear)) {
			//$html = '<ul class="messages">';
			foreach ($allMessages as $type => $messages) {
				foreach ($messages as $message) {
					$html .= '<div class="alert fade in alert-' . $type . '">'
						. '<a class="close" data-dismiss="alert">×</a>'
						. '<p>' . $message . '</p>'
						. '</div>';
				}
			}
			//$html .= '</ul>';
		}
		return $html;
	}
}