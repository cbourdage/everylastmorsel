<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initLocale()
	{
        $locale = new Zend_Locale('en_US');
        Zend_Registry::set('Zend_Locale', $locale);
    }

	/**
     * Add the config to the registry
     */
    protected function _initConfig()
    {
        Zend_Registry::set('config', $this->getOptions());
    }

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
             ->appendStylesheet('/file-bin/css/960/960.css')
             ->appendStylesheet('/file-bin/css/screen.css');
        $this->_view->headTitle('Every Last Morsel');
        $this->_view->headTitle()->setSeparator('|');
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

	protected function _initModuleAutoloader()
	{
    	$this->_resourceLoader = new Zend_Application_Module_Autoloader(array(
    		'namespace' => 'Elm',
    		'basePath' => APPLICATION_PATH
    	));

    	$this->_resourceLoader->addResourceTypes(array(
    		'models' => array(
    			'path' => 'models',
    			'namespace' => 'Model'
    		),
			'modelResources' => array(
    			'path' => 'models/resources',
    			'namespace' => 'Model_Resource'
    		)
    	));
    }

	/*protected function _initLogging()
	{
		$this->bootstrap('frontController');
	    $logger = new Zend_Log();

	    $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/../data/logs/app.log');
        $logger->addWriter($writer);

        if ($this->getEnvironment() == 'production') {
            $logger->addFilter(new Zend_Log_Filter_Priority(Zend_Log::CRIT));
        }

        $this->_logger = $logger;
        Zend_Registry::set('log', $logger);
	}*/

	// @TODO create routes.ini to setup all routes
	protected function _initRoutes()
	{
		$this->bootstrap('frontController');
        $frontController = $this->getResource('frontController');

        $route = new Zend_Controller_Router_Route(
		    'u/:alias',
		    array(
		    	'controller' => 'user',
        		'action' => 'view',
				'alias' => ''
		    ),
			array(
				'alias' => '[a-zA-Z-_0-9]+'
			)
		);
		$frontController->getRouter()->addRoute('user', $route);

		$route = new Zend_Controller_Router_Route(
		    'help',
		    array(
		    	'controller' => 'index',
        		'action' => 'help',
		    )
		);
		$frontController->getRouter()->addRoute('cms', $route);
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
     * Retrieve resource vodel object singleton
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
				$logDir = APPLICATION_PATH . '/data/log/';
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
	{;
		return ucwords(str_replace('/', '_', $name));
    }
}

