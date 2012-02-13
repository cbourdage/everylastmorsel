<?php

class Elm_Model_Resource_Plot_Image extends Colony_Db_Table
{
    protected $_name = 'plot_images';

	protected $_primary = 'image_id';

	protected $_referenceMap = array(
		'Image' => array(
			'columns' => 'plot_id',
			'refTableClass' => 'Elm_Model_Resource_Plot',
			'refColumns' => 'plot_id'
		)
	);
}

