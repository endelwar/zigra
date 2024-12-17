<?php

class Zigra_Controller
{
    protected $tplVar;
    protected $registry;

    /**
     * Zigra_Controller constructor.
     */
    public function __construct(protected Zigra_Request $request, protected array $params, ?Aura\Session\Session $session_manager = null)
    {
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
    public function getRequest(): Zigra_Request
    {
        return $this->request;
    }

    /**
     * Retrieves the route params.
     *
     * @return array The route params
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Retrieves a single param from the route.
     *
     * @return mixed The route param
     */
    public function getParam(string $key)
    {
        return $this->params[$key] ?? null;
    }

    /**
     * Retrieves the current Zigra_Router object.
     *
     * @return Zigra_Router The current Zigra_Router implementation instance
     */
    public function getRouter(): Zigra_Router
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
    public function getUser(object $userclass, ?Aura\Session\Session $sessionManager = null): Zigra_User
    {
        return Zigra_User::singleton($userclass, $sessionManager);
    }

    /**
     * Executes an application defined process prior to execution of this Zigra_Controller object.
     *
     * By default, this method is empty.
     */
    public function preExecute(): void
    {
    }

    /**
     * Execute an application defined process immediately after execution of this Zigra_Controller object.
     *
     * By default, this method is empty.
     */
    public function postExecute(): void
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
     */
    public static array $statusTexts = [
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
    ];

    /**
     * Forwards current action to a new route.
     *
     * @param string      $routename  Name of route
     * @param array       $params     array of parameter for the route
     * @param int|null    $statuscode HTTP status code
     * @param string|null $anchor     string to append as hash anchor
     */
    public function forward(string $routename, array $params = [], ?int $statuscode = null, ?string $anchor = null): void
    {
        $url = Zigra_Router::generate($routename, $params);

        if ($url) {
            if ((null !== $statuscode) && \array_key_exists($statuscode, self::$statusTexts)) {
                header('HTTP/1.1 ' . $statuscode . ' ' . self::$statusTexts[$statuscode]);
            }
            if (null !== $anchor) {
                $url .= '#' . $anchor;
            }
            header('Location: ' . $url);
        } else {
            $this->forward404();
        }

        exit;
    }

    /**
     * Forwards current action to the default 404 error action.
     *
     * @param string|null $message Message of the generated exception
     */
    public function forward404(?string $message = null): void
    {
        header('HTTP/1.1 404 ' . self::$statusTexts[404]);
        $this->registry->set('templatename', 'error-404.html.twig');
        $this->registry->set('message', $message);
    }

    /**
     * Redirect to specified url.
     *
     * @param string $url        url to be redirected
     * @param int    $statuscode HTTP status code
     */
    public function redirect(string $url, int $statuscode = 307): void
    {
        header('Location: ' . $url, true, $statuscode);
        exit;
    }
}
