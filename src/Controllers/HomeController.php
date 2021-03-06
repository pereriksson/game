<?php

declare(strict_types=1);

namespace pereriksson\Controllers;

use Nyholm\Psr7\Factory\Psr17Factory;
use pereriksson\Http\HttpInterface;
use Psr\Http\Message\ResponseInterface;
use pereriksson\Util\Util;
use pereriksson\Session\SessionInterface;

/**
 * Controller for the index route.
 */
class HomeController
{
    private $util;
    private $session;
    private $http;

    public function __construct(Util $util, SessionInterface $session, HTTPInterface $http)
    {
        $this->util = $util;
        $this->session = $session;
        $this->http = $http;
    }

    public function index(): ResponseInterface
    {
        $psr17Factory = new Psr17Factory();

        $data = [
            "title" => "Home",
            "component" => "components/home.twig"
        ];

        $data["navItems"] = [
            [
                "url" => $this->util->url("/"),
                "label" => "Home"
            ],
            [
                "url" => $this->util->url("/twentyone"),
                "label" => "Game 21"
            ],
            [
                "url" => $this->util->url("/yatzy"),
                "label" => "Yatzy"
            ],
            [
                "url" => $this->util->url("/session"),
                "label" => "Session"
            ]
        ];

        $body = $this->util->renderTwigView("index.twig", $data);

        return $psr17Factory
            ->createResponse(200)
            ->withBody($psr17Factory->createStream($body));
    }
}
