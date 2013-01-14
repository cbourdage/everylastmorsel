<?php

class Elm_Model_Yield_Transaction extends Colony_Model_Abstract
{
	public function _construct()
    {
        $this->_init('yield_transaction');
    }

	public function _beforeSave()
	{
		/*if (!$this->getYieldId()) {
			$this->setYieldId($this->getYield()->getId());
		}

		if (!$this->getPlotCropId()) {
			$this->setPlotCropId($this->getYield()->getPlotCropId());
		}*/
	}
}
