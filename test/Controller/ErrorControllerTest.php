<?php

declare(strict_types=1);

namespace pereriksson\Controllers;

use pereriksson\Http\MockHttpClient;
use pereriksson\Session\MockSession;
use pereriksson\Util\Util;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * Test cases for the controller Debug.
 */
class ErrorControllerTest extends TestCase
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
        $controller = new ErrorController($util, $session, $http);
        $this->assertInstanceOf("\pereriksson\Controllers\ErrorController", $controller);
    }

    public function test404()
    {
        $http = new MockHttpClient();
        $util = new Util($http);
        $session = new MockSession();
        $http = new MockHttpClient();
        $controller = new ErrorController($util, $session, $http);
        $res = $controller->do404();
        $this->assertInstanceOf("\Psr\Http\Message\ResponseInterface", $res);
    }

    public function test405()
    {
        $http = new MockHttpClient();
        $util = new Util($http);
        $session = new MockSession();
        $http = new MockHttpClient();
        $controller = new ErrorController($util, $session, $http);
        $allowed = ["GET"];
        $res = $controller->do405($allowed);
        $this->assertInstanceOf("\Psr\Http\Message\ResponseInterface", $res);
    }
}
