<?php

class Zigra_User
{
    protected static $instance;
    protected static $_user;
    protected $userclass;

    const COOKIE_EXPIRE = 8640000; //60*60*24*90 seconds = 100 days by default
    const COOKIE_PATH = "/"; //Available in whole domain

    public function __construct($userclass)
    {
        $this->userclass = $userclass;

        /* Prevent JavaScript from reading Session cookies */
        ini_set('session.cookie_httponly', '1');

        /* Start Session */
        if (session_id() == '') {
            session_set_cookie_params(3600); // Set session cookie duration to 1 hour
            session_start();
        }

        /* Check if last session is from the same pc */
        if (!isset($_SESSION['last_ip'])) {
            $_SESSION['last_ip'] = $_SERVER['REMOTE_ADDR'];
        }
        if ($_SESSION['last_ip'] !== $_SERVER['REMOTE_ADDR']) {
            /* Clear the SESSION */
            $_SESSION = array();
            /* Destroy the SESSION */
            session_unset();
            session_destroy();
        }
    }

    public function destroy()
    {
        self::$instance = null;
    }

    public static function singleton($userclass)
    {
        $className = __CLASS__;
        if (!isset(self::$instance)) {
            self::$instance = new $className($userclass);
        }
        if (strtolower(get_class(self::$instance->userclass)) !== strtolower($userclass)) {
            self::$instance = new $className($userclass);
        }
        return self::$instance;
    }

    public function loggedIn()
    {
        $status = false;
        if (isset($_SESSION['member_valid']) && $_SESSION['member_valid']) {
            if (isset($_SESSION['member_type']) && ($_SESSION['member_type'] == $this->userclass->getUserType())) {
                $status = true;
            }
        }
        /* controlla COOKIE */
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
                //utente trovato, verificare credenziali
                if ($this->verify($password, $user->password) == true) {
                    /* If correct create session */
                    session_regenerate_id();
                    self::$_user = $user;
                    $_SESSION['member'] = $user->toArray();
                    unset($_SESSION['member']['password']);
                    $_SESSION['member_id'] = $user->id;
                    $_SESSION['member_valid'] = true;
                    $_SESSION['member_type'] = $user::USERTYPE;

                    /* User Remember me feature? */
                    //$this->createNewCookie($user->id);

                    return true;
                } else {
                    // password sbagliata
                    return false;
                }
            } else {
                // utente non trovato, riproponi login
                return false;
            }
        } else {
            // mancano dati, riproponi login
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
        } else {
            return false;
        }
    }

    /**
     * Logout
     * @param string|null $routeName
     * @param array $routeParams
     * @throws Exception
     */
    public function logout($routeName = null, $routeParams = array())
    {
        // Clear the SESSION
        $_SESSION = array();
        // Destroy the SESSION
        session_unset();
        session_destroy();

        // Redirect
        if (null == $routeName) {
            $routeName = 'homepage';
        }
        $url = Zigra_Router::generate($routeName, $routeParams);
        header("Location: " . $url);
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
            ->setFrom(array('server@zigra.dev' => 'Zigra App'))
            ->setTo($email)
            ->setBody($password);

        $mailer->send($message);
    }
}
