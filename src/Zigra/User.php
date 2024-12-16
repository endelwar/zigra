<?php

class Zigra_User
{
    protected static $instance;
    protected static $_user;

    private static \Aura\Session\Session $sessionManager;

    public function __construct(protected $userclass, Aura\Session\Session $sessionManager)
    {
        self::$sessionManager = $sessionManager;
        /* Start Session */
        if (false === self::$sessionManager->isStarted()) {
            self::$sessionManager->start();
        }
    }

    public function destroy(): void
    {
        self::$instance = null;
    }

    /**
     * @param $userclass
     */
    public static function singleton($userclass, Aura\Session\Session $sessionManager = null): self
    {
        if ($sessionManager instanceof Aura\Session\Session) {
            self::$sessionManager = $sessionManager;
        } else {
            $session_factory = new \Aura\Session\SessionFactory();
            self::$sessionManager = $session_factory->newInstance($_COOKIE);
        }
        self::$sessionManager->resume();
        if (null === self::$instance) {
            self::$instance = new self($userclass, self::$sessionManager);
        }
        if (strtolower(self::$instance->userclass::class) !== strtolower((string) $userclass)) {
            self::$instance = new self($userclass, self::$sessionManager);
        }

        return self::$instance;
    }

    public function loggedIn(): bool
    {
        $status = false;
        if (
            isset($_SESSION['member_valid'], $_SESSION['member_type']) &&
            $_SESSION['member_valid'] &&
            $_SESSION['member_type'] === $this->userclass->getUserType()
        ) {
            $status = true;
        }

        return $status;
    }

    public function login(string $email, string $password): bool
    {
        if ($email && $password) {
            $userclass = $this->userclass;
            $user = $userclass::findOneByEmail($email);
            if ($user) {
                // User found, verify password
                if (true === static::verify($password, $user->password)) {
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
     */
    public function setAsLoggedIn($user): bool
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

            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Verify Password.
     */
    public static function verify(string $password, string $existingHash): bool
    {
        if (password_verify($password, $existingHash)) {
            return true;
        }

        return false;
    }

    /**
     * Logout.
     *
     * @throws Exception
     */
    public function logout(string $routeName = null, array $routeParams = []): void
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
        return password_hash((string) $password, \PASSWORD_DEFAULT);
    }

    public static function generatePassword($length = 8): string
    {
        $password = '';
        $possiblechars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        for ($i = 0; $i < $length; ++$i) {
            $password .= mb_substr($possiblechars, random_int(0, $length - 1), 1);
        }

        return $password;
    }
}
