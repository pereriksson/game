<?php

declare(strict_types=1);

namespace pereriksson\Controllers;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use pereriksson\Http\HttpInterface;
use Psr\Http\Message\ResponseInterface;
use pereriksson\Util\Util;
use pereriksson\Session\SessionInterface;

/**
 * Controller for the session routes.
 */
class SessionController
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
            "title" => "Session",
            "session" => $this->session->getSession(),
            "component" => "components/session.twig"
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


    public function destroy(): ResponseInterface
    {
        $this->session->destroy();

        return (new Response())
            ->withStatus(301)
            ->withHeader("Location", $this->util->url("/session"));
    }
}
