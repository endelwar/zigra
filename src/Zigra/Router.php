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
     * @return Zigra_Router
     */
    public static function singleton()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @param Zigra_Request $request
     * @param bool $resetProperties
     * @param null|Aura\Session\Session $session_manager
     * @throws \InvalidArgumentException
     */
    public static function route(Zigra_Request $request, $resetProperties = false, $session_manager = null)
    {
        self::singleton();
        $controller = 'error';
        $action = 'error404';
        $routefound = self::process($request, $resetProperties);

        if ($routefound) {
            $controller = self::$_controller;
            $action = self::$_action;
        }

        self::callControllerAction(
            $controller,
            $action,
            $request,
            array_merge(self::$_defaults, self::$_args),
            $session_manager
        );
    }

    /**
     * @param string $controllerName
     * @param string $action
     * @param Zigra_Request $request
     * @param array $params
     * @param null|Aura\Session\Session $session_manager
     * @param bool $isError
     * @throws \InvalidArgumentException
     * @return bool|null
     */
    private static function callControllerAction(
        $controllerName,
        $action,
        Zigra_Request $request,
        array $params,
        $session_manager = null,
        $isError = false
    ) {
        // TODO: make this contruct indipendent from app path
        $classFileName = '../app/controller/' . $controllerName . 'Controller.php';
        try {
            if (file_exists($classFileName)) {
                include_once $classFileName;

                $fullClassName = ucfirst($controllerName) . 'Controller';
                /** @var Zigra_Controller $controller */
                $controller = new $fullClassName($request, $params, $session_manager);
                if (!is_callable([$controller, $action])) {
                    throw new Zigra_Exception(
                        'Cannot call module: ' . $controllerName . '->' . $action
                    );
                }

                if ($session_manager) {
                    $registry = $session_manager->getSegment('zigra\registry');
                } else {
                    $registry = Zigra_Registry::getInstance();
                }
                $registry->set('controller', $controllerName);
                $registry->set('action', $action);

                $controller->preExecute();
                $controller->$action($request);
                $controller->postExecute();

                return true;
            }

            // are we coming from a declared error?
            if ($isError === false) {
                throw new Zigra_Exception(
                    'Cannot find class ' . $controllerName . ' (' . $classFileName . ')'
                );
            }

            // Issue a 404 error classname file doesn't exists
            header('HTTP/1.0 404 Not Found');
            echo '<h1>404 Not Found</h1>';
            echo 'The page that you have requested could not be found.';
            exit();
        } catch (Zigra_Exception $e) {
            Zigra_Exception::renderError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Generate a short url for given arguments.
     *
     * @param Zigra_Request $request request
     * @param bool $resetProperties if reset or not all singleton proprieties
     *
     * @return bool true when route is found or false if not found
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
                $args = [];
                foreach ($route[0]['variables'] as $variable) {
                    $args[$variable] = array_shift($matches);
                }

                self::$matchedRoute = $routeName;
                self::$_controller = (self::$_controller === null || $resetProperties)
                    ? $route[0]['defaults']['controller'] : self::$_controller;
                self::$_action = (self::$_action === null || $resetProperties)
                    ? $route[0]['defaults']['action'] : self::$_action;
                self::$_args = (self::$_args === null || $resetProperties)
                    ? $args : self::$_args;
                self::$_defaults = (self::$_defaults === null || $resetProperties)
                    ? $route[0]['defaults'] : self::$_defaults;

                if (isset($request_parts['query'])) {
                    parse_str($request_parts['query'], $query_args);
                    self::$_args = array_merge($query_args, self::$_args);
                }

                return true;
            }
        }
        self::$_controller = (self::$_controller === null || $resetProperties)
            ? $request->getController()
            : self::$_controller;
        self::$_action = (self::$_action === null || $resetProperties) ? $request->getAction() : self::$_action;
        self::$_args = (self::$_args === null || $resetProperties) ? $request->getArgs() : self::$_args;
        self::$_defaults = [];

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
     * @throws \InvalidArgumentException
     */
    public static function map(
        $name,
        $pattern,
        array $defaults,
        array $requirements = [],
        array $options = []
    ) {
        self::singleton();
        $route = new Zigra_Route($pattern, $defaults, $requirements, $options);
        self::$_routeCollection->add($name, $route);
        self::$_compiledRouteCollection->add($name, $route->compile());
    }

    /**
     * Generate a short url for given arguments.
     *
     * @param string $name optional name of route to be used (if not set the route will be selected on given params)
     * @param array $params the arguments to be processed by the created url
     *
     * @throws InvalidArgumentException if route name is not found
     *
     * @return string|false generated url or false on error
     */
    public static function generate($name = '', array $params = [])
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
        /** @var Zigra_Route $route */
        $route = $routes[$name];

        // let the route do the actual url creation
        return $route->generate($params);
    }
}
