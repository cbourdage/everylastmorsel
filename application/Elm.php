<?php
/**
 * @TODO Plot People
 * @TODO Plot People - Follow/Following
 * @TODO Plot Association - Pending People?
 * @TODO Plot Association - Add People??
 */

class Elm extends Zend_Application_Bootstrap_Bootstrap
{
	/**
     * Run the application
     *
     * Checks to see that we have a default controller directory. If not, an
     * exception is thrown.
     *
     * If so, it registers the bootstrap with the 'bootstrap' parameter of
     * the front controller, and dispatches the front controller.
     *
     * @return mixed
     * @throws Zend_Application_Bootstrap_Exception
     */
    public function run()
    {
		$front   = $this->getResource('FrontController');
        $default = $front->getDefaultModule();
        if (null === $front->getControllerDirectory($default)) {
            throw new Zend_Application_Bootstrap_Exception(
                'No default controller directory registered with front controller'
            );
        }

        $front->setParam('bootstrap', $this);

		// Apply all updates
		Elm_Model_Setup::applyAllUpdates();

		// Dispatch and go
        $response = $front->dispatch();
        if ($front->returnResponse()) {
            return $response;
		}
    }

	/**
     * Add the config to the registry
     */
    protected function _initConfig()
    {
        Zend_Registry::set('config', $this->getOptions());
    }

	/**
	 *
	 */
	protected function _initLocale()
	{
        $locale = new Zend_Locale('en_US');
        Zend_Registry::set('Zend_Locale', $locale);
    }

	/**
	 *
	 */
	protected function _initMailTransport()
	{
		if (self::getAppConfig('initTestEmail')) {
			try {
				$config = array(
					'auth' => 'login',
					'username' => 'everylastmorsel',
					'password' => '305cdafd-d010-420e-bcb5-658839c3b2aa',
					'ssl' => 'tls',
					'port' => 587
				);

				$mailTransport = new Zend_Mail_Transport_Smtp('smtp.mandrillapp.com', $config);
				Zend_Mail::setDefaultTransport($mailTransport);
			} catch (Zend_Exception $e) {
				//Do something with exception
			}
		}
	}

	/**
	 * Initialize view settings
	 */
	protected function _initViewSettings()
	{
        $this->bootstrap('view');
        $this->_view = $this->getResource('view');
        $this->_view->setEncoding('UTF-8');
        $this->_view->doctype('HTML5');
        $this->_view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        $this->_view->headMeta()->appendHttpEquiv('Content-Language', 'en-US');
        $this->_view->headLink()
            ->appendStylesheet('/file-bin/css/960/reset.css')
		 	//->appendStylesheet('/file-bin/css/960/960.css')
			//->appendStylesheet('/file-bin/css/smoothness/jquery-ui-1.8.18.custom.css')
            ->appendStylesheet('/file-bin/css/screen.css');
		$this->_view->headScript()
			->appendFile('/file-bin/js/lib/jquery-1.7.1.min.js', 'text/javascript')
            //->appendFile('/file-bin/js/lib/jquery-ui-1.8.18.custom.min.js', 'text/javascript')
            ->appendFile('/file-bin/js/lib/plugins.js', 'text/javascript')
			->appendFile('/file-bin/js/elm.js', 'text/javascript');
        $this->_view->headTitle('Every Last Morsel')
			->setSeparator(' | ');
    }

    /**
     * Database Initialization
     *
     */
	protected function _initDb()
	{
		$resource = $this->getPluginResource('db');
		$db = $resource->getDbAdapter();
	    $db->setFetchMode(Zend_Db::FETCH_OBJ);
		Zend_Db_Table::setDefaultAdapter($db);
	    Zend_Registry::set('db', $db);
	}

    /**
     * Saving sessions to db support
     */
    protected function _initSession()
	{
		$config = array(
			'name'           => 'session',
			'primary'        => 'id',
			'modifiedColumn' => 'modified',
			'dataColumn'     => 'data',
			'lifetimeColumn' => 'lifetime'
		);
		//create your Zend_Session_SaveHandler_DbTable and set the save handler for Zend_Session
		//Zend_Session::setSaveHandler(new Zend_Session_SaveHandler_DbTable($config));
		//Zend_Session::setOptions($this->getOption('session'));

        Zend_Session::setOptions(array('cookie_domain' => Elm::getAppConfig('domain')));

		//self::log(Zend_Session::getOptions());
	}

	protected function _initModuleAutoloader()
	{
    	$this->_resourceLoader = new Zend_Application_Module_Autoloader(array(
    		'namespace' => 'Elm',
    		'basePath' => APPLICATION_PATH
    	));

		// @TODO autoloader setup for controllers
    	$this->_resourceLoader->addResourceTypes(array(
			'models' => array(
    			'path' => 'models',
    			'namespace' => 'Model'
    		),
			'modelResources' => array(
    			'path' => 'models/Resources',
    			'namespace' => 'Model_Resource'
    		)
    	));
    }

	/**
	 * @TODO create routes.ini to setup all routes Zend_Controller_Router_Rewrite::addConfig(Zend_Config $config, $section = null)
	 * 
	 * @return void
	 */
	protected function _initRoutes()
	{
		$this->bootstrap('FrontController');
        $frontController = $this->getResource('FrontController');

		// user
        $route = new Zend_Controller_Router_Route(
		    //'u/:alias',
		    'u/:u',
		    array(
		    	'controller' => 'user',
        		'action' => 'about',
				//'alias' => ''
				'u' => ''
		    ),
			array(
				//'alias' => '[a-zA-Z-_0-9\.]+'
				'u' => '[0-9]+'
			)
		);
		$frontController->getRouter()->addRoute('user', $route);

		// user plots
		$route = new Zend_Controller_Router_Route(
		    'u/plots/:u',
		    array(
		    	'controller' => 'user',
        		'action' => 'plots',
				'u' => ''
		    ),
			array(
				'u' => '[0-9]+'
			)
		);
		$frontController->getRouter()->addRoute('user-plots', $route);

		// user crops
		$route = new Zend_Controller_Router_Route(
		    'u/crops/:u',
		    array(
		    	'controller' => 'user',
        		'action' => 'crops',
				'u' => ''
		    ),
			array(
				'u' => '[0-9]+'
			)
		);
		$frontController->getRouter()->addRoute('user-crops', $route);

		// plot
        $route = new Zend_Controller_Router_Route(
		    'p/:p',
		    array(
		    	'controller' => 'plot',
        		'action' => 'view',
				'p' => ''
		    ),
			array(
				'p' => '\d+'
			)
		);
		$frontController->getRouter()->addRoute('plot', $route);

		// plot photos
        $route = new Zend_Controller_Router_Route(
		    'p/photos/:p',
		    array(
		    	'controller' => 'plot',
        		'action' => 'photos',
				'p' => ''
		    ),
			array(
				'p' => '\d+'
			)
		);
		$frontController->getRouter()->addRoute('plot-photos', $route);

		// plot crops
        $route = new Zend_Controller_Router_Route(
		    'p/crops/:p',
		    array(
		    	'controller' => 'plot',
        		'action' => 'crops',
				'p' => ''
		    ),
			array(
				'p' => '\d+'
			)
		);
		$frontController->getRouter()->addRoute('plot-crops', $route);

		// plot users
        $route = new Zend_Controller_Router_Route(
		    'p/people/:p',
		    array(
		    	'controller' => 'plot',
        		'action' => 'people',
				'p' => ''
		    ),
			array(
				'p' => '\d+'
			)
		);
		$frontController->getRouter()->addRoute('plot-people', $route);
		$route = new Zend_Controller_Router_Route(
		    'p/edit/:p',
		    array(
		    	'controller' => 'plot',
        		'action' => 'edit',
				'p' => ''
		    ),
			array(
				'p' => '\d+'
			)
		);
		$frontController->getRouter()->addRoute('plot-edit', $route);
		$route = new Zend_Controller_Router_Route(
		    'p/edit-save/:p',
		    array(
		    	'controller' => 'plot',
        		'action' => 'edit-save',
				'p' => ''
		    ),
			array(
				'p' => '\d+'
			)
		);
		$frontController->getRouter()->addRoute('plot-edit-save', $route);
        $route = new Zend_Controller_Router_Route(
            'p/settings/:p',
            array(
                'controller' => 'plot',
                'action' => 'settings',
                'p' => ''
            ),
            array(
                'p' => '\d+'
            )
        );
        $frontController->getRouter()->addRoute('plot-settings', $route);
        $route = new Zend_Controller_Router_Route(
            'p/settings-save/:p',
            array(
                'controller' => 'plot',
                'action' => 'settings-save',
                'p' => ''
            ),
            array(
                'p' => '\d+'
            )
        );
        $frontController->getRouter()->addRoute('plot-settings-save', $route);

		// about-us
		$route = new Zend_Controller_Router_Route(
		    'about-us',
		    array(
		    	'controller' => 'index',
        		'action' => 'about',
		    )
		);
		$frontController->getRouter()->addRoute('about-us', $route);

		// help
		$route = new Zend_Controller_Router_Route(
		    'help',
		    array(
		    	'controller' => 'index',
        		'action' => 'help',
		    )
		);
		$frontController->getRouter()->addRoute('help', $route);

		// faq
		$route = new Zend_Controller_Router_Route(
		    'faq',
		    array(
		    	'controller' => 'index',
        		'action' => 'faq',
		    )
		);
		$frontController->getRouter()->addRoute('faq', $route);

		// coming-soon
		$route = new Zend_Controller_Router_Route(
		    'coming-soon',
		    array(
		    	'controller' => 'index',
        		'action' => 'coming-soon',
		    )
		);
		$frontController->getRouter()->addRoute('coming-soon', $route);
    }

	/**
	 * Returns base application directory path
	 *
	 * @static
	 * @param null $path path to directory
	 * @return string
	 */
	public static function getBaseDir($path = null)
	{
		/*$pattern = '/^(media|file-bin)/i';
		if (preg_match($pattern, $path)) {
			$path = '/http/' . $path;
		}*/
		//Elm::log(realpath(APPLICATION_PATH . '/../') . DIRECTORY_SEPARATOR . $path);
		return realpath(APPLICATION_PATH . '/../') . DIRECTORY_SEPARATOR . $path;
	}

	/**
	 * Returns the base url for the app
	 *
	 * @static
	 * @param null $type
	 * @return string
	 */
	public static function getBaseUrl($type = null)
	{
		$baseUrl = self::getAppConfig('baseurl');
		return $type !== null ? $baseUrl . $type : $baseUrl;
	}

	/**
	 * Returns the app. namespace config settings
	 *
	 * @static
	 * @param null $option
	 * @return mixed
	 */
	public static function getAppConfig($option = null)
	{
		$config = Zend_Registry::get('config');
		if ($option === null) {
			return $config['app'];
		}
		return $config['app'][$option];
	}

	/**
     * Retrieve model object
     *
     * @param   string $modelClass
     * @return  Colony_Model_Abstract
     */
    public static function getModel($modelClass = '')
    {
		$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$class = implode(array(
	  			$bootstrap->_getNamespace(),
				'Model',
				$bootstrap->_getInflected($modelClass)
		 	), '_'
		);
        return new $class();
    }

	/**
     * Retrieve model object
     *
     * @param   string $modelClass
     * @return  Colony_Model_Abstract
     */
    public static function getResourceModel($modelClass = '')
    {
		$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$class = implode(array(
	  			$bootstrap->_getNamespace(),
				'Model_Resource',
				$bootstrap->_getInflected($modelClass)
		 	), '_'
		);
        return new $class();
    }

    /**
     * Retrieve model object singleton
     *
     * @param   string $modelClass
     * @param   array $arguments
     * @return  Colony_Model_Abstract
     */
    public static function getSingleton($modelClass='', array $arguments=array())
    {
        $registryKey = '_singleton/'.$modelClass;
        if (!Zend_Registry::isRegistered($registryKey)) {
            Zend_Registry::set($registryKey, self::getModel($modelClass, $arguments));
        }
        return Zend_Registry::get($registryKey);
    }

	/**
     * Retrieve resource model object singleton
     *
     * @param   string $modelClass
     * @param   array $arguments
     * @return  object
     */
    public static function getResourceSingleton($modelClass = '', array $arguments = array())
    {
        $registryKey = '_resource_singleton/'.$modelClass;
		if (!Zend_Registry::isRegistered($registryKey)) {
            Zend_Registry::set($registryKey, self::getResourceModel($modelClass, $arguments));
        }
        return Zend_Registry::get($registryKey);
    }

	/**
     * Return new exception by module to be thrown
     *
     * @param string $module
     * @param string $message
     * @param integer $code
     * @return Colony_Exception
     */
    public static function exception($module = 'Colony', $message = '', $code = 0)
    {
        $className = $module.'_Exception';
        return new $className($message, $code);
    }

	/**
	 * Throw Exception
	 *
	 * @param string $message
	 * @param string $messageStorage
	 * @throws Colony_Exception
	 * @return void
	 */
    public static function throwException($message, $messageStorage = null)
    {
        if ($messageStorage && ($storage = self::getSingleton($messageStorage))) {
            $storage->addError($message);
        }
        throw new Colony_Exception($message);
    }

    /**
     * log facility
     *
     * @param string $message
     * @param integer $level
     * @param string $file
     * @param bool $forceLog
     */
    public static function log($message, $level = null, $file = '', $forceLog = false)
    {
        static $loggers = array();

        $level  = is_null($level) ? Zend_Log::DEBUG : $level;
        $file = empty($file) ? 'app.log' : $file;

        try {
            if (!isset($loggers[$file])) {
				$logDir = APPLICATION_PATH . '/var/log/';
                $logFile = $logDir . $file;

                if (!is_dir($logDir)) {
                    mkdir($logDir, 0777);
                }

                if (!file_exists($logFile)) {
                    file_put_contents($logFile, '');
                    chmod($logFile, 0777);
                }

				$writer = new Zend_Log_Writer_Stream($logFile);
				$format = '%timestamp% %priorityName% (%priority%): %message%' . PHP_EOL;
                $writer->setFormatter(new Zend_Log_Formatter_Simple($format));
                $loggers[$file] = new Zend_Log($writer);
            }

            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }

            $loggers[$file]->log($message, $level);
        }
        catch (Exception $e) {
        }
    }

    /**
     * Write exception to log
     *
     * @param Exception $e
     */
    public static function logException(Exception $e)
    {
        self::log("\n" . $e->__toString(), Zend_Log::ERR, 'exception.log');
    }

	/**
	 * Write exception to log
	 *
	 * @param $s
	 */
    public static function logGoogleRequest($s)
    {
        self::log($s, Zend_Log::DEBUG, 'google/' . date('Y-m-d-') . 'google-request.log');
    }

	/**
	 * Profile method tracks timing of execution of certain actions. The name
	 * parameter namespaces the action that is passed in. Accepts actions of
	 * "start", "end" and null
	 *
	 * @static
	 * @param string $name
	 * @param string $action null|start|end
	 */
	public static function profile($name, $action = null)
	{
		static $profiles = array();
		try {
            if (!isset($profiles[$name])) {
				$profiles[$name] = array(
					'start' => microtime(true),
					'checkpoint' => array(),
					'end' => null
				);
            }

			if ($action == 'end') {
				$profiles[$name]['end'] = microtime(true);
				self::log($profiles[$name], Zend_Log::INFO, 'profiles/' . $name . '.log');
				self::log(
					"Total Time: " . ($profiles[$name]['end'] - $profiles[$name]['start']),
					Zend_Log::INFO,
					'profiles/' . $name . '.log'
				);
			} else {
				$profiles[$name]['checkpoint'][] = microtime(true) - $profiles[$name]['start'];
			}
        }
        catch (Exception $e) {
        }
	}

	/**
     * Classes are named spaced using their module name
     * this returns that module name or the first class name segment.
     *
     * @return string This class namespace
     */
    private function _getNamespace()
    {
		return $this->getAppNamespace();
    }

    /**
     * Get the inflected name
     *
     * @param  string $name
     * @return string
     */
    private function _getInflected($name)
	{
		$name = ucwords(str_replace(array('/', '_'), ' ', $name));
		return str_replace(' ', '_', $name);
    }

	/**
	 * Put in place because of row objects - should move out
	 * and create own row objects for each row... abstract the zend_table_row
	 * results out.
	 *
	 * @TODO abstract out the Zend_Table query results
	 *
	 * @static
	 * @param $data
	 * @return array
	 */
	public static function toArray($data)
	{
		if (is_object($data)) {
			$data = get_object_vars($data);
		}
 		// if is array loop through to check nested items for objects
		if (is_array($data)) {
			return array_map(__METHOD__, $data);
		} else {
			// Return array
			return $data;
		}
	}
}

