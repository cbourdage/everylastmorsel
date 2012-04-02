<?php

class Elm_Model_Resource_Plot_Crop extends Colony_Db_Table
{
	protected $_name = 'plot_crops';

	protected $_primary = 'entity_id'; //array('crop_id', 'plot_id', 'user_id');

	protected $_referenceMap = array(
		'Crop' => array(
			'columns' => 'plot_id',
			'refTableClass' => 'Elm_Model_Resource_Plot',
			'refColumns' => 'plot_id'
		)
	);
}
