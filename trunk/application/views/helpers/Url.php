<?php

/**
 * Helper for making easy links and getting urls that depend on the routes and router
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Elm_View_Helper_Url extends Zend_View_Helper_Url
{
    /**
     * Generates an url given the name of a route.
     *
     * @access public
     *
     * @param  string $path url path 'user/create'
     * @param  mixed $params the query string parameters key=>value pair
     * @return string url
     */
    public function url($path, array $params = array())
    {
		$route = 'default';
		if (isset($params['_route'])) {
			$route = $params['_route'];
			unset($params['_route']);
		}
		$encode = false;
		if (isset($params['_encode'])) {
			$encode = $params['_encode'];
			unset($params['_encode']);
		}
		$reset = true;
		if (isset($params['_reset'])) {
			$params['_reset'];
			unset($params['_reset']);
		}

		if ($path !== null) {
			list ($controller, $action) = explode('/', $path);

			if ($controller == null) {
				$urlOptions = array(
					'controller' => 'index',
					'action' => 'index',
				);
			} elseif ($action == null) {
				$urlOptions = array(
					'controller' => $controller,
					'action' => 'index',
				);
			} else {
				$urlOptions = array(
					'controller' => $controller,
					'action' => $action,
				);
			}
		}

		foreach ($params as $key => $value) {
			$urlOptions[$key] = $value;
		}

		$router = Zend_Controller_Front::getInstance()->getRouter();
        return $router->assemble($urlOptions, $route, $reset, $encode);
    }

	public function test()
	{
		echo '<script>alert("testing");</script>';
	}
}
