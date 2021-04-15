<?php

declare(strict_types=1);

namespace pereriksson\Controllers;

use pereriksson\Session\MockSession;
use pereriksson\Util\Util;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use pereriksson\Http\MockHttpClient;

/**
 * Test cases for the controller Debug.
 */
class SessionControllerTest extends TestCase
{
    /**
     * Try to create the controller class.
     */
    public function testCreate()
    {
        $http = new MockHttpClient();
        $util = new Util($http);
        $session = new MockSession();
        $http = new MockHttpClient();
        $controller = new SessionController($util, $session, $http);
        $this->assertInstanceOf("\pereriksson\Controllers\SessionController", $controller);
    }

    public function testIndex()
    {
        $http = new MockHttpClient();
        $util = new Util($http);
        $session = new MockSession();
        $http = new MockHttpClient();
        $controller = new SessionController($util, $session, $http);
        $res = $controller->index();
        $this->assertInstanceOf("\Psr\Http\Message\ResponseInterface", $res);
    }

    public function testDestroy()
    {
        $http = new MockHttpClient();
        $util = new Util($http);
        $session = new MockSession();
        $http = new MockHttpClient();
        $controller = new SessionController($util, $session, $http);
        $res = $controller->destroy();
        $this->assertInstanceOf("\Psr\Http\Message\ResponseInterface", $res);
        $this->assertEquals(1, $session->getSessionStatus());
    }
}
