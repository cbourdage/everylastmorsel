<?php

/**
 * Elm_IndexController
 */
require_once 'controllers/AbstractController.php';

class Elm_NewsletterController extends Elm_AbstractController
{
	/**
	 * @return void
	 */
    public function signupAction()
    {
		if ($email = $this->getRequest()->getParam('email', null)) {
			if (Elm::getModel('newsletter')->signUp($email)) {
				Elm::getSingleton('user/session')->addSuccess('Thank you for signing up!');
			} else {
				Elm::getSingleton('user/session')->addError('Gasp! There was an error adding you to the list. Try again shortly.');
			}
		}
    }
}

