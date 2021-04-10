<?php

declare(strict_types=1);

namespace pereriksson\Controllers;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use pereriksson\Util\Util;

/**
 * Controller for the session routes.
 */
class SessionController
{
    private $util;
    private $session;

    public function __construct(Util $util, \pereriksson\Session\Session $session)
    {
        $this->util = $util;
        $this->session = $session;
    }

    public function index(): ResponseInterface
    {
        $psr17Factory = new Psr17Factory();

        $data = [
            "title" => "Session",
            "session" => $_SESSION,
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
        $this->util->destroySession();

        return (new Response())
            ->withStatus(301)
            ->withHeader("Location", $this->util->url("/session"));
    }
}
