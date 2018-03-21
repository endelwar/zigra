<?php

class Zigra_User
{
    protected static $instance;
    protected static $_user;
    protected $userclass;
    /** @var \Aura\Session\Session */
    private static $sessionManager;

    public function __construct($userclass, Aura\Session\Session $sessionManager)
    {
        $this->userclass = $userclass;
        self::$sessionManager = $sessionManager;
        /* Start Session */
        if (self::$sessionManager->isStarted() === false) {
            self::$sessionManager->start();
        }

        /* Check if last session is from the same pc */
        if (!isset($_SESSION['last_ip'])) {
            $_SESSION['last_ip'] = $_SERVER['REMOTE_ADDR'];
        }
        if ($_SESSION['last_ip'] !== $_SERVER['REMOTE_ADDR']) {
            self::$sessionManager->destroy();
        }
    }

    public function destroy()
    {
        self::$instance = null;
    }

    public static function singleton($userclass)
    {
        if (null === self::$sessionManager) {
            $session_factory = new \Aura\Session\SessionFactory();
            self::$sessionManager = $session_factory->newInstance($_COOKIE);
        }
        self::$sessionManager->resume();
        if (null === self::$instance) {
            self::$instance = new self($userclass, self::$sessionManager);
        }
        if (strtolower(get_class(self::$instance->userclass)) !== strtolower($userclass)) {
            self::$instance = new self($userclass, self::$sessionManager);
        }

        return self::$instance;
    }

    public function loggedIn()
    {
        $status = false;
        if (
            isset($_SESSION['member_valid'], $_SESSION['member_type']) &&
            $_SESSION['member_valid'] &&
            $_SESSION['member_type'] === $this->userclass->getUserType()
        ) {
            $status = true;
        }

        // check COOKIE
        /*
         elseif (isset($_COOKIE['remember_me_id']) && isset($_COOKIE['remember_me_hash']))
          {
          //TODO codice per cookie
          }
        */

        return $status;
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login($email, $password)
    {
        if ($email && $password) {
            $userclass = $this->userclass;
            $user = $userclass::findOneByEmail($email);
            if ($user) {
                // User found, verify password
                if (static::verify($password, $user->password) === true) {
                    $this->setAsLoggedIn($user);

                    return true;
                }

                // Wrong password
                return false;
            }

            // User not found, show login again
            return false;
        }

        // Missing data, show login again
        return false;
    }

    /**
     * @param \Doctrine_Record $user
     * @return bool
     */
    public function setAsLoggedIn($user)
    {
        try {
            /* If correct create session */
            self::$sessionManager->regenerateId();
            self::$_user = $user;
            $_SESSION['member'] = $user->toArray();
            unset($_SESSION['member']['password']);
            $_SESSION['member_id'] = $user->id;
            $_SESSION['member_valid'] = true;
            $_SESSION['member_type'] = $user::USERTYPE;

            $_SESSION['userObj'] = $user;

            /* User Remember me feature? */

            //$this->createNewCookie($user->id);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Verify Password
     * @param string $password
     * @param string $existingHash
     * @return bool
     */
    public static function verify($password, $existingHash)
    {
        if (password_verify($password, $existingHash)) {
            return true;
        }

        return false;
    }

    /**
     * Logout
     * @param string|null $routeName
     * @param array $routeParams
     * @throws Exception
     */
    public function logout($routeName = null, array $routeParams = [])
    {
        // Destroy the SESSION
        self::$sessionManager->destroy();

        // Redirect
        if (null === $routeName) {
            $routeName = 'homepage';
        }
        $url = Zigra_Router::generate($routeName, $routeParams);
        header('Location: ' . $url);
    }

    public function getuserobj()
    {
        return self::$_user;
    }

    public static function generateHashedPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function generatePassword($length = 8)
    {
        $password = '';
        $possiblechars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        for ($i = 0; $i < $length; $i++) {
            $password .= substr($possiblechars, mt_rand(0, $length - 1), 1);
        }

        return $password;
    }

    public static function emailPassword($email, $password)
    {
        $transport = Swift_SmtpTransport::newInstance('localhost', 25);
        $mailer = Swift_Mailer::newInstance($transport);
        // TODO string translation
        // TODO make everything a parameter
        $message = Swift_Message::newInstance('Zigra App - Email Password')
            ->setFrom(['server@zigra.dev' => 'Zigra App'])
            ->setTo($email)
            ->setBody($password);

        $mailer->send($message);
    }
}
