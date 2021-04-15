<?php

namespace pereriksson\Session;

class MockSession implements SessionInterface
{
    private $sessionName;
    private $session;
    private $status;

    public function startUniqueSession()
    {
        $sessionName = preg_replace("/[^a-z\d]/i", "", __DIR__);
        $this->startSession($sessionName);
    }

    public function getSession()
    {
        return $this->session;
    }

    public function startSession($sessionName)
    {
        $this->status = PHP_SESSION_ACTIVE;
        $this->session = [];
        $this->sessionName = $sessionName;
        return true;
    }

    public function getSessionName()
    {
        return $this->sessionName;
    }

    public function keyExist($name)
    {
        return isset($this->session[$name]);
    }

    public function set($name, $value)
    {
        $this->session[$name] = $value;
        return true;
    }

    public function remove($name)
    {
        unset($this->session[$name]);
        return true;
    }

    public function get($name)
    {
        if (!$this->keyExist($name)) {
            return null;
        }

        return $this->session[$name];
    }

    public function destroy(): void
    {
        $this->session = null;
        $this->sessionName = null;
        $this->status = PHP_SESSION_NONE;
    }

    public function getSessionStatus()
    {
        return $this->status;
    }
}
