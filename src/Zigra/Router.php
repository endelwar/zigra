<?php

class Zigra_Router
{

    protected static $_controller;
    protected static $_action;
    protected static $_args;
    protected static $_compiledRouteCollection;
    protected static $_routeCollection;
    private static $_instance;

    private function __construct()
    {
        self::$_routeCollection = new Zigra_Route_Collection();
        self::$_compiledRouteCollection = new Zigra_Route_Collection();
    }

    public static function singleton()
    {
        if (!isset(self::$_instance)) {
            $className = __CLASS__;
            self::$_instance = new $className;
        }
        return self::$_instance;
    }

    public static function route(Zigra_Request $request, $resetProperties = false)
    {
        self::singleton();

        $routefound = self::_Process($request, $resetProperties);
        if ($routefound) {
            $className = self::$_controller;
            $classFileName = 'app/controller/' . $className . 'Controller.php';
            if (file_exists($classFileName)) {
                include_once $classFileName;

                $fullClassName = $className . 'Controller';
                self::$_controller = new $fullClassName($request, self::$_args);
                if (is_callable(array(self::$_controller, self::$_action))) {
                    $registry = Zigra_Registry::getInstance();
                    $registry->set('controller', $className);
                    $registry->set('action', self::$_action);

                    call_user_func_array(array(self::$_controller, 'preExecute'), array($request));
                    call_user_func_array(array(self::$_controller, self::$_action), array($request));
                    call_user_func_array(array(self::$_controller, 'postExecute'), array($request));

                    return;
                } else {
                    throw new Zigra_Exception('Impossibile richiamare il modulo: <pre>' . $className . '->' . self::$_action . '</pre>');
                }
            } else {
                throw new Zigra_Exception('Impossibile trovare la classe <pre>' . $className . ' (' . $classFileName . ')</pre>');

                //self::route(new Zigra_Request('error'), true);
            }
        } else {
            // 404 route not found
            $classFileName = 'app/controller/errorController.php';
            if (file_exists($classFileName)) {
                include_once $classFileName;
                $errorprocess = new errorController($request, self::$_args);

                $registry = Zigra_Registry::getInstance();
                $registry->set('controller', 'error');
                $registry->set('action', 'error404');

                call_user_func_array(array($errorprocess, 'error404'), array($request));
            } else {
                header('HTTP/1.0 404 Not Found');
                echo "<h1>404 Not Found</h1>";
                echo "The page that you have requested could not be found.";
                exit();
            }
        }
    }

    /**
     * Generate a short url for given arguments.
     *
     * @param Zigra_Request $request         Request.
     * @param boolean $resetProperties If reset or not all singleton proprieties.
     *
     * @return boolean true when route is found or false if not found.
     */
    private static function _Process(Zigra_Request $request, $resetProperties = false)
    {
        $routes = array();
        $routes = self::$_compiledRouteCollection->routes;
        foreach ($routes as $route) {
            preg_match($route[0]['regex'], $request->getRequest(), $matches);
            //var_dump($route[0]['regex'], $request, $matches);

            if (count($matches)) {

                //array_shift($matches);
                //var_dump('matches', $matches);

                //rimuovi id numerici dall'array
                foreach ($matches as $k => &$v) {
                    if (is_numeric($k)) {
                        unset($matches[$k]);
                    }
                }
                $args = array();
                //var_dump('route 0', $route[0]['variables']);
                foreach ($route[0]['variables'] as $variable) {
                    $args[$variable] = array_shift($matches);
                }
                //var_dump('args', $args);
                self::$_controller = (!isset(self::$_controller) || $resetProperties) ? $route[0]['defaults']['controller'] : self::$_controller;
                self::$_action = (!isset(self::$_action) || $resetProperties) ? $route[0]['defaults']['action'] : self::$_action;
                self::$_args = (!isset(self::$_args) || $resetProperties) ? $args : self::$_args;
                //var_dump(self::$_args);echo '<hr>';

                return true;
            }
        }
        self::$_controller = (!isset(self::$_controller) || $resetProperties) ? $request->getController(
        ) : self::$_controller;
        self::$_action = (!isset(self::$_action) || $resetProperties) ? $request->getAction() : self::$_action;
        self::$_args = (!isset(self::$_args) || $resetProperties) ? $request->getArgs() : self::$_args;
        //var_dump(self::$_args);echo '<hr>';

        return false;
    }

    /**
     * compile and Add route to mapped array
     *
     * @param strign $name         name of the route
     * @param string $pattern      pattern matching the route
     * @param array $defaults     defaults parameters
     * @param array $requirements TODO write docs
     * @param array $options      TODO write docs
     *
     * @return void
     *
     * */
    public static function map(
        $name,
        $pattern,
        array $defaults,
        array $requirements = array(),
        array $options = array()
    ) {
        self::singleton();
        $route = new Zigra_Route($pattern, $defaults, $requirements, $options);
        $compiledRoute = $route->Compile();
        self::$_routeCollection->Add($name, $route);
        self::$_compiledRouteCollection->Add($name, $compiledRoute);
    }

    /**
     * Generate a short url for given arguments.
     *
     * @param string $name   Optional name of route to be used (if not set the route will be selected based on given params).
     * @param array $params The arguments to be processed by the created url.
     *
     * @throws Exception if route name is not found
     *
     * @return mixed string With created url or false on error.
     */

    public static function generate($name = '', array $params = array())
    {
        self::singleton();

        // reference to the route used for url creation
        $route = null;
        $routes = self::$_routeCollection->routes;

        if ($name) {
            // a certain route should be used
            if (!isset($routes[$name])) {
                // this route does not exist, so we abort here
                return false;
            }
            // use this route
            $route = $routes[$name];
        } else {
            throw new Exception('Cannot find route named "' . $name . '"');
        }

        // let the route do the actual url creation
        $url = $route->generate($params);

        // return the result
        return $url;
    }

}