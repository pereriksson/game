<?php

declare(strict_types=1);

namespace pereriksson\Session;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * Test cases for the controller Debug.
 */
class SessionTest extends TestCase
{
    /**
     * Try to create the controller class.
     */
    public function testStartUniqueSession()
    {
        $session = new MockSession();
        $session->startUniqueSession();
        $this->assertEquals($session->getSessionStatus(), PHP_SESSION_ACTIVE);
    }

    public function testDestroySession()
    {
        $session = new MockSession();
        $session->destroy();
        $this->assertEquals($session->getSessionStatus(), PHP_SESSION_NONE);
    }

    public function testSet()
    {
        $session = new MockSession();
        $name = "a";
        $value = "b";
        $session->set($name, $value);
        $this->assertEquals($session->get($name), $value);
        $this->assertEquals($session->keyExist($name), true);
        $session->remove($name);
        $this->assertEquals($session->get($name), null);
        $this->assertEquals($session->keyExist($name), false);
    }

    public function testStartSession()
    {
        $session = new MockSession();
        $sessionName = "abc123";
        $session->startSession($sessionName);
        $this->assertEquals($session->getSessionName(), $sessionName);
    }
}
