<?php

class Zigra_Router
{
    protected static $_controller;
    protected static $_action;
    protected static $_args;
    protected static $_defaults;
    protected static $_compiledRouteCollection;
    protected static $_routeCollection;
    private static $matchedRoute;
    private static $instance;

    private function __construct()
    {
        self::$_routeCollection = new Zigra_Route_Collection();
        self::$_compiledRouteCollection = new Zigra_Route_Collection();
    }

    /**
     * @return $this
     */
    public static function singleton()
    {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className();
        }

        return self::$instance;
    }

    /**
     * @param Zigra_Request $request
     * @param bool $resetProperties
     * @param null $session_manager
     */
    public static function route(Zigra_Request $request, $resetProperties = false, $session_manager = null)
    {
        self::singleton();

        $routefound = self::process($request, $resetProperties);
        if ($routefound) {
            self::callControllerAction(
                self::$_controller,
                self::$_action,
                $request,
                array_merge(self::$_defaults, self::$_args),
                $session_manager
            );
        } else {
            // 404 route not found
            self::callControllerAction(
                'error',
                'error404',
                $request,
                array_merge(self::$_defaults, self::$_args),
                $session_manager,
                true
            );
        }
    }

    /**
     * @param $className
     * @param $action
     * @param Zigra_Request $request
     * @param array $params
     * @param null $session_manager
     * @param bool $isError
     * @throws \InvalidArgumentException
     */
    private static function callControllerAction(
        $className,
        $action,
        Zigra_Request $request,
        array $params,
        $session_manager = null,
        $isError = false
    ) {
        // TODO: make this contruct indipendent from app path
        $classFileName = '../app/controller/' . $className . 'Controller.php';
        try {
            if (file_exists($classFileName)) {
                include_once $classFileName;

                $fullClassName = ucfirst($className) . 'Controller';
                $controller = new $fullClassName($request, $params, $session_manager);
                if (is_callable(array($controller, $action))) {
                    if ($session_manager) {
                        $registry = $session_manager->getSegment('zigra\registry');
                    } else {
                        $registry = Zigra_Registry::getInstance();
                    }
                    $registry->set('controller', $className);
                    $registry->set('action', $action);

                    call_user_func_array(array($controller, 'preExecute'), array($request));
                    call_user_func_array(array($controller, $action), array($request));
                    call_user_func_array(array($controller, 'postExecute'), array($request));

                    return;
                } else {
                    throw new Zigra_Exception(
                        'Cannot call module: ' . $className . '->' . $action
                    );
                }
            } else {
                if ($isError === false) {
                    throw new Zigra_Exception(
                        'Cannot find class ' . $className . ' (' . $classFileName . ')'
                    );
                } else {
                    header('HTTP/1.0 404 Not Found');
                    echo '<h1>404 Not Found</h1>';
                    echo 'The page that you have requested could not be found.';
                    exit();
                }
            }
        } catch (Zigra_Exception $e) {
            Zigra_Exception::renderError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Generate a short url for given arguments.
     *
     * @param Zigra_Request $request Request.
     * @param bool $resetProperties If reset or not all singleton proprieties.
     *
     * @return bool true when route is found or false if not found.
     */
    private static function process(Zigra_Request $request, $resetProperties = false)
    {
        $routes = self::$_compiledRouteCollection->routes;
        foreach ($routes as $routeName => $route) {
            $request_parts = parse_url($request->getRequest());
            if (!isset($request_parts['path'])) {
                $request_parts['path'] = null;
            }
            preg_match($route[0]['regex'], $request_parts['path'], $matches);

            if (count($matches)) {
                //remove numeric index from array
                $matches_filter = array_filter(array_keys($matches), 'is_string');
                $matches = array_intersect_key($matches, array_flip($matches_filter));
                $args = array();
                foreach ($route[0]['variables'] as $variable) {
                    $args[$variable] = array_shift($matches);
                }

                self::$matchedRoute = $routeName;
                self::$_controller = (!isset(self::$_controller) || $resetProperties)
                    ? $route[0]['defaults']['controller'] : self::$_controller;
                self::$_action = (!isset(self::$_action) || $resetProperties)
                    ? $route[0]['defaults']['action'] : self::$_action;
                self::$_args = (!isset(self::$_args) || $resetProperties)
                    ? $args : self::$_args;
                self::$_defaults = (!isset(self::$_defaults) || $resetProperties)
                    ? $route[0]['defaults'] : self::$_defaults;

                if (isset($request_parts['query'])) {
                    parse_str($request_parts['query'], $query_args);
                    self::$_args = array_merge(self::$_args, $query_args);
                }

                return true;
            }
        }
        self::$_controller = (!isset(self::$_controller) || $resetProperties)
            ? $request->getController()
            : self::$_controller;
        self::$_action = (!isset(self::$_action) || $resetProperties) ? $request->getAction() : self::$_action;
        self::$_args = (!isset(self::$_args) || $resetProperties) ? $request->getArgs() : self::$_args;
        self::$_defaults = array();

        return false;
    }

    /**
     * return the route name matched by the router
     *
     * @return string|null
     */
    public static function getMatchedRouteName()
    {
        return self::$matchedRoute;
    }

    /**
     * compile and add route to mapped array
     *
     * @param string $name name of the route
     * @param string $pattern pattern matching the route
     * @param array $defaults defaults parameters
     * @param array $requirements TODO write docs
     * @param array $options TODO write docs
     *
     * @return void
     */
    public static function map(
        $name,
        $pattern,
        array $defaults,
        array $requirements = array(),
        array $options = array()
    ) {
        self::singleton();
        $route = new Zigra_Route($pattern, $defaults, $requirements, $options);
        $compiledRoute = $route->compile();
        self::$_routeCollection->add($name, $route);
        self::$_compiledRouteCollection->add($name, $compiledRoute);
    }

    /**
     * Generate a short url for given arguments.
     *
     * @param string $name Optional name of route to be used (if not set the route will be selected on given params).
     * @param array $params The arguments to be processed by the created url.
     *
     * @throws InvalidArgumentException if route name is not found
     *
     * @return string|false Generated url or false on error.
     */

    public static function generate($name = '', array $params = array())
    {
        self::singleton();

        $routes = self::$_routeCollection->routes;

        if (!$name) {
            throw new InvalidArgumentException('Cannot find route named "' . $name . '"');
        }
        // a certain route should be used
        if (!isset($routes[$name])) {
            // this route does not exist, so we abort here
            return false;
        }
        // use this route
        $route = $routes[$name];

        // let the route do the actual url creation
        return $route->generate($params);
    }
}
