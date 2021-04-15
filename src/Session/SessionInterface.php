<?php

namespace pereriksson\Session;

interface SessionInterface
{
    public function startUniqueSession();

    public function startSession($sessionName);

    public function getSessionName();

    public function keyExist($name);

    public function set($name, $value);

    public function remove($name);

    public function get($name);

    public function destroy();

    public function getSessionStatus();

    public function getSession();
}
