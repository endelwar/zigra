<?php

class Zigra_Controller
{
    protected $request = null;
    protected $params = null;
    protected $tplVar = null;
    protected $registry = null;

    /**
     * Zigra_Controller constructor.
     * @param Zigra_Request $request
     * @param array $params
     * @param null $session_manager
     */
    public function __construct(Zigra_Request $request, array $params, $session_manager = null)
    {
        $this->request = $request;
        $this->params = $params;
        if ($session_manager) {
            $this->registry = $session_manager->getSegment('zigra\registry');
            $this->tplVar = $session_manager->getSegment('zigra\tplvar');
        } else {
            $this->registry = Zigra_Registry::getInstance();
            $this->tplVar = Zigra_Registry_Tplvar::getInstance();
        }
    }

    /**
     * Retrieves the current Zigra_Request object.
     *
     * @return Zigra_Request The current Zigra_Request implementation instance
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Retrieves the route params.
     *
     * @return array The route params
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Retrieves a single param from the route.
     *
     * @param string $key
     * @return Mixed The route param
     */
    public function getParam($key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }

        return;
    }

    /**
     * Retrieves the current Zigra_Router object.
     *
     * @return Zigra_Router The current Zigra_Router implementation instance
     */
    public function getRouter()
    {
        return Zigra_Router::singleton();
    }

    /**
     * Retrieves the current Zigra_User object.
     *
     * @param object $userclass User class that retrieves user data from database
     *
     * @return Zigra_User The current Zigra_User implementation instance
     */
    public function getUser($userclass)
    {
        return Zigra_User::singleton($userclass);
    }

    /**
     * Executes an application defined process prior to execution of this Zigra_Controller object.
     *
     * By default, this method is empty.
     */
    public function preExecute()
    {
    }

    /**
     * Execute an application defined process immediately after execution of this Zigra_Controller object.
     *
     * By default, this method is empty.
     */
    public function postExecute()
    {
    }

    /**
     * Status codes translation table.
     *
     * The list of codes is complete according to the
     * {@link http://www.iana.org/assignments/http-status-codes/ Hypertext Transfer Protocol (HTTP) Status Code Registry}
     * (last updated 2012-02-13).
     *
     * Unless otherwise noted, the status code is defined in RFC2616.
     *
     * @var array
     */
    public static $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing', // RFC2518
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status', // RFC4918
        208 => 'Already Reported', // RFC5842
        226 => 'IM Used', // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect', // RFC-reschke-http-status-308-07
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot', // RFC2324
        422 => 'Unprocessable Entity', // RFC4918
        423 => 'Locked', // RFC4918
        424 => 'Failed Dependency', // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal', // RFC2817
        426 => 'Upgrade Required', // RFC2817
        428 => 'Precondition Required', // RFC6585
        429 => 'Too Many Requests', // RFC6585
        431 => 'Request Header Fields Too Large', // RFC6585
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)', // RFC2295
        507 => 'Insufficient Storage', // RFC4918
        508 => 'Loop Detected', // RFC5842
        510 => 'Not Extended', // RFC2774
        511 => 'Network Authentication Required', // RFC6585
    );

    /**
     * Forwards current action to a new route.
     *
     * @param string $routename Name of route
     * @param array $params array of parameter for the route
     * @param int $statuscode HTTP status code
     * @param string $anchor string to append as hash anchor
     *
     * @return void
     */
    public function forward($routename, $params = array(), $statuscode = null, $anchor = null)
    {
        $url = Zigra_Router::generate($routename, $params);

        if ($url) {
            if ((null !== $statuscode) && (array_key_exists($statuscode, self::$statusTexts))) {
                header('HTTP/1.1 ' . $statuscode . ' ' . self::$statusTexts[$statuscode], true);
            }
            if (null !== $anchor) {
                $url = $url . '#' . $anchor;
            }
            header('Location: ' . $url);
        } else {
            $this->forward404();
        }

        die();
    }

    /**
     * Forwards current action to the default 404 error action.
     *
     * @param string $message Message of the generated exception
     *
     * @return void
     */
    public function forward404($message = null)
    {
        header('HTTP/1.1 404 ' . self::$statusTexts[404]);
        $this->registry->set('templatename', 'error-404.html.twig');
        $this->registry->set('message', $message);
    }

    /**
     * Redirect to specified url
     *
     * @param string $url url to be redirected
     * @param int $statuscode HTTP status code
     *
     * @return void
     */
    public function redirect($url, $statuscode = 307)
    {
        header('Location: ' . $url, true, $statuscode);
        die();
    }
}
