<?php

declare(strict_types=1);

namespace pereriksson\Controllers;

use pereriksson\Http\MockHttpClient;
use pereriksson\Session\MockSession;
use pereriksson\Util\Util;
use pereriksson\Yatzy\Yatzy;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * Test cases for the controller Debug.
 */
class YatzyControllerTest extends TestCase
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
        $controller = new YatzyController($util, $session, $http);
        $this->assertInstanceOf("\pereriksson\Controllers\YatzyController", $controller);
    }

    public function testIndex()
    {
        $http = new MockHttpClient();
        $util = new Util($http);
        $session = new MockSession();
        $http = new MockHttpClient();
        $yatzy = new Yatzy();
        $session->set("yatzy", $yatzy);
        $controller = new YatzyController($util, $session, $http);
        $res = $controller->index();
        $this->assertInstanceOf("\Psr\Http\Message\ResponseInterface", $res);
    }

    public function testAction()
    {
        $http = new MockHttpClient();
        $util = new Util($http);
        $session = new MockSession();
        $http = new MockHttpClient();
        $controller = new YatzyController($util, $session, $http);
        $res = $controller->action();
        $this->assertInstanceOf("\Psr\Http\Message\ResponseInterface", $res);
    }

    public function testLeave()
    {
        $http = new MockHttpClient();
        $util = new Util($http);
        $session = new MockSession();
        $http = new MockHttpClient();
        $controller = new YatzyController($util, $session, $http);
        $http->setPost("action", "leave");
        $controller->action();
        $this->assertEquals($session->get("yatzy"), null);
    }

    public function testStart()
    {
        $http = new MockHttpClient();
        $util = new Util($http);
        $session = new MockSession();
        $http = new MockHttpClient();
        $controller = new YatzyController($util, $session, $http);
        $http->setPost("action", "start");
        $controller->action();
        $this->assertInstanceOf("\pereriksson\Yatzy\Yatzy", $session->get("yatzy"));
    }

    public function testReset()
    {
        $http = new MockHttpClient();
        $util = new Util($http);
        $session = new MockSession();
        $http = new MockHttpClient();
        $controller = new YatzyController($util, $session, $http);
        $yatzy = new Yatzy();
        $yatzy->addPlayer("Me");
        $session->set("yatzy", $yatzy);
        $http->setPost("action", "reset");
        $controller->action();
        $this->assertEquals("ones", $session->get("row"));
    }

    public function testThrow()
    {
        $http = new MockHttpClient();
        $util = new Util($http);
        $session = new MockSession();
        $http = new MockHttpClient();
        $controller = new YatzyController($util, $session, $http);
        $yatzy = new Yatzy();
        $yatzy->addPlayer("Me");
        $yatzy->newRound();
        $session->set("yatzy", $yatzy);
        $http->setPost("action", "throw");
        $controller->action();
        $this->assertEquals(1, $yatzy->getThrowRound());
    }
}
