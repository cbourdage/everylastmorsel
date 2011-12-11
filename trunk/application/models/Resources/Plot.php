<?php

class Elm_Model_Resource_Plot extends Colony_Db_Table
{
    protected $_name = 'plot';

	protected $_primary = 'plot_id';

	public function loadByLatLong($obj, $lat, $long)
	{
		
	}
}

