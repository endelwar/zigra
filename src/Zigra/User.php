<?php

class Zigra_User extends User
{

    private static $_instance;
    private static $_user;

    const COOKIE_EXPIRE = 8640000; //60*60*24*90 seconds = 100 days by default
    const COOKIE_PATH = "/"; //Available in whole domain

    function __construct()
    {
        /* Prevent JavaScript from reaidng Session cookies */
        ini_set('session.cookie_httponly', true);

        /* Start Session */
        if (session_id() == '') {
            session_start();
        }

        /* Check if last session is fromt he same pc */
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

    public static function Singleton()
    {
        if (!isset(self::$_instance)) {
            $className = __CLASS__;
            self::$_instance = new $className;
        }
        return self::$_instance;
    }

    public function loggedIn()
    {
        if (isset($_SESSION['member_valid']) && $_SESSION['member_valid']) {
            /* Return true */
            $status = true;
        } /* controlla COOKIE */
        /* elseif (isset($_COOKIE['remember_me_id']) && isset($_COOKIE['remember_me_hash']))
          {
          //TODO codice per cookie
          } */
        else {
            /* Return false */
            $status = false;
        }

        return $status;
    }

    /* Login */

    public function login($email, $password)
    {
        if ($email && $password) {
            $user = User::findOneByEmail($email);
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
            // riproponi login
            return false;
        }
    }

    /* Verify Password */

    public function verify($password, $existingHash)
    {
        /* Hash new password with old hash */
        $hash = crypt($password, $existingHash);
        /* Do Hashs match? */
        if ($hash === $existingHash) {
            return true;
        } else {
            return false;
        }
    }

    /* Logout */

    public function logout()
    {
        /* Log */
        if (isset($_SESSION['member_id'])) {
            $user_id = $_SESSION['member_id'];
        }
        /* else
          {
          $user_id = $_COOKIE['remember_me_id'];
          } */

        /* Clear the SESSION */
        $_SESSION = array();
        /* Destroy the SESSION */
        session_unset();
        session_destroy();
        /* Delete all old cookies and user_logged */
        /* if (isset($_COOKIE['remember_me_id']))
          {
          $this->deleteCookie($_COOKIE['remember_me_id']);
          } */

        /* Redirect */
        $url = Zigra_Router::generate('homepage');
        header("Location: " . $url);
    }

    public function getuserobj()
    {
        return self::$_user;
    }

    public static function generateHashedPassword($password)
    {
        global $salt_string;
        $rounds = mt_rand(5000, 99999);
        $hashedPassword = crypt($password, '$6$rounds=' . $rounds . '$' . $salt_string . '$');

        return $hashedPassword;
    }

    public static function generatePassword($length = 8)
    {
        $password = '';
        $possiblechars = 'abcdefghilmnopqrstuvz0123456789';
        for ($i = 0; $i < $length; $i++) {
            $password .= substr($possiblechars, mt_rand(0, $length - 1), 1);
        }

        return $password;
    }

    public static function emailPassword($email, $password)
    {
        require_once 'lib/vendor/swift-mailer/lib/swift_required.php';

        $transport = Swift_SmtpTransport::newInstance('localhost', 25);
        $mailer = Swift_Mailer::newInstance($transport);
        // TODO tradurre frase
        // TODO parametrizzare tutto!!!!!
        $message = Swift_Message::newInstance('Zigra App - Email Password')
            ->setFrom(array('server@zigra.dev' => 'Zigra App'))
            ->setTo($email)
            ->setBody($password);

        $result = $mailer->send($message);
    }

}