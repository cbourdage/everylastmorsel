<?php


class Elm_Model_Plot extends Colony_Model_Abstract
{
	/**
	 * @var array
	 */
	private $_users = array();

	/**
	 * @var array
	 */
	private $_images = array();

	/**
	 *
	 */
	public function _construct()
    {
        $this->_init('plot');
    }

	/**
	 *
	 */
	public function _beforeSave()
	{
	}

	/**
     * Load plot by lat & long
     *
     * @param   string $lat
     * @param   string $long
     * @return  Elm_Model_Plot
     */
    public function loadByLatLong($lat, $long)
    {
        $this->_getResource()->loadByLatLong($this, $lat, $long);
        return $this;
    }

	/**
	 * Returns all plots
	 *
	 * @return mixed
	 */
	public function getAllPlots()
	{
		return $this->_getResource()->getAllPlots();
	}

	/**
	 * Get users
	 *
	 * @return mixed
	 */
	public function getUsers()
	{
		if (count($this->_users) < 1) {
			foreach ($this->getUserIds() as $id => $role) {
				if ($role != Elm_Model_Resource_Plot::ROLE_CREATOR) {
					$user = Bootstrap::getModel('user')->load($id);
					$user->setUserRole($role);
					$this->_users[] = $user;
				}
			}
		}
		return $this->_users;
	}

    /**
     * Send email with new account specific information
     *
	 * @TODO send new plot email - http://stackoverflow.com/questions/1218191/how-can-i-make-email-template-in-zend-framework
	 *
	 * @param string $backUrl
     * @return Elm_Model_User
     */
    public function sendNewPlotEmail($backUrl = '')
    {
		try {
			$EmailTemplate = new Elm_Model_Email_Template(array('template' => 'new-plot.phtml'));
			$EmailTemplate->setParams(array(
				'plot' => $this
			));
			$EmailTemplate->send(array('email' => 'collin.bourdage@gmail.com', 'name' => 'Collin Bourdage'));
		} catch(Exception $e) {
			Bootstrap::logException($e);
		}
        return $this;
    }

	/**
	 * Associates a user and a plot with an assigned role
	 *
	 * @param $userId int
	 * @param $role string
	 * @return Elm_Model_Plot
	 */
	public function associateUser($userId, $role)
	{
		$this->_getResource()->associateUser($this, $userId, $role);
        return $this;
	}

	/**
	 * Checks if the user is associated with this plot
	 *
	 * @param $user
	 * @return bool
	 */
	public function isAssociated($user)
	{
		foreach ($this->getUsers() as $u) {
			if ($user->getId() == $u->getId()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns a plots url
	 *
	 * @return string
	 */
	public function getUrl()
	{
		$url = '/p/' . $this->getId();
		return $url;
	}

	/**
	 * Returns the plots images
	 * @return array
	 */
	public function getImages()
	{
		if (!$this->_images) {
			$this->_images = $this->_getResource()->getImages($this->getId());
		}

		return $this->_images;
	}

	/**
	 * Adds a new image to the plot
	 *
	 * @param array $params
	 * @return bool
	 */
	public function addImages($params)
	{
		$destination = $this->_getImageDestination();
		$image = Bootstrap::getModel('plot_image');

		$adapter = new Zend_File_Transfer_Adapter_Http();
		$adapter->setDestination($destination)
			->addValidator('Size', false, 102400)	// limit to 100K
			->addValidator('Extension', false, 'jpg,png,gif,jpeg'); // only JPEG, PNG, and GIFs

		$files = $adapter->getFileInfo();
		foreach ($files as $file => $info) {
			list($temp, $ext) = explode('.', $info['name']);
			$newFilename = md5($temp) . '.' . $ext;
			$imageIdx = preg_replace('/[^\d]/', '', $file);

			try {
				// Rename filter
				$adapter->addFilter('Rename', array(
					'target' => $destination . DIRECTORY_SEPARATOR . $newFilename,
					'overwrite' => true
				));

				if ($adapter->receive($file)) {
					$image->setData($info);
					$image->setData('exif_data', exif_read_data($info['destination'] . DIRECTORY_SEPARATOR . $newFilename));
					$image->setPlotId($this->getId())
						->setCaption($params['caption'][$imageIdx]);

					// Set image data
					$image->setThumbnail(Elm_Model_Plot_Image::getPlotImageUrl($this) . '/' . $newFilename)
						->setFull(Elm_Model_Plot_Image::getPlotImageUrl($this) . '/' . $newFilename);
					$image->save();
					$image->reset();
				} else {
					Bootstrap::log($adapter->getMessages(), Zend_Log::ERR, Elm_Model_Plot_Image::LOG_FILE);
					throw new Colony_Exception('Image upload encountered an error. Please try again.', '600');
				}
			} catch (Exception $e) {
				Bootstrap::logException($e);
				Bootstrap::getSingleton('user/session')->addException('Bah! [' . $e->getCode() . '] ' . $e->getMessage());
				return false;
			}
		}

		return true;
	}

	/**
	 * @return string
	 */
	protected function _getImageDestination()
	{
		$destination = Bootstrap::getBaseDir(Elm_Model_Plot_Image::DESTINATION_DIR) . Elm_Model_Plot_Image::getPlotImagePath($this);
		if (!is_dir($destination)) {
			mkdir($destination, 0777, true);
		}

		return $destination;
	}
}