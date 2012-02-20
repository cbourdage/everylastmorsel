<?php

class Elm_Model_Resource_Plot_Status extends Colony_Db_Table
{
    protected $_name = 'plot_status_updates';

	protected $_primary = 'update_id';

	protected $_referenceMap = array(
		'Status' => array(
			'columns' => 'plot_id',
			'refTableClass' => 'Elm_Model_Resource_Plot',
			'refColumns' => 'plot_id'
		)
	);
}

