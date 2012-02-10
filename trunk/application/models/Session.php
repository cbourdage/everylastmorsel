<?php

class Elm_Model_Session extends Colony_Session
{
	public function __construct()
	{
		parent::__construct('elm');
	}
}