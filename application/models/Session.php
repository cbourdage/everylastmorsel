<?php

class Elm_Model_Session extends Colony_Session
{
	public function __construct()
	{
        /*if (!Zend_Session::sessionExists()) {
            parent::__construct('elm');
        }*/
        parent::__construct('elm');
	}
}