<?php

namespace pereriksson\Session;

class PhpSession implements SessionInterface
{
    public function startUniqueSession()
    {
        $this->startSession(preg_replace("/[^a-z\d]/i", "", __DIR__));
    }

    public function startSession($sessionName)
    {
        session_name($sessionName);
        session_start();
        return true;
    }

    public function getSession()
    {
        return $_SESSION;
    }

    public function getSessionName()
    {
        return session_name();
    }

    public function keyExist($name)
    {
        return isset($_SESSION[$name]);
    }

    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
        return true;
    }

    public function remove($name)
    {
        unset($_SESSION[$name]);
        return true;
    }

    public function get($name)
    {
        if (!$this->keyExist($name)) {
            return null;
        }

        return $_SESSION[$name];
    }

    public function destroy(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                $this->getSessionName(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    public function getSessionStatus()
    {
        return session_status();
    }
}
