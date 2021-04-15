<?php

declare(strict_types=1);

namespace pereriksson\Controllers;

use Nyholm\Psr7\Factory\Psr17Factory;
use pereriksson\Http\HttpInterface;
use pereriksson\Session\SessionInterface;
use pereriksson\Util\Util;
use Psr\Http\Message\ResponseInterface;
use pereriksson\Session\PhpSession;

/**
 * Controller for error routes.
 */
class ErrorController
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

    public function do404(): ResponseInterface
    {
        $psr17Factory = new Psr17Factory();

        $data = [
            "header" => "404",
            "message" => "The page you are requesting is not here. You may also checkout the HTTP response code, it should be 404.",
            "component" => "components/error.twig"
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
                "url" => $this->util->url("/session"),
                "label" => "Session"
            ]
        ];

        $body = $this->util->renderTwigView("index.twig", $data);

        return $psr17Factory
            ->createResponse(404)
            ->withBody($psr17Factory->createStream($body));
    }


    public function do405(array $allowed): ResponseInterface
    {
        $psr17Factory = new Psr17Factory();

        $data = [
            "header" => "405",
            "message" => "Method is not allowed. Allowed methods are: "
                . implode(", ", $allowed),
            "component" => "components/error.twig"
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
                "url" => $this->util->url("/session"),
                "label" => "Session"
            ]
        ];

        $body = $this->util->renderTwigView("index.twig", $data);

        return $psr17Factory
            ->createResponse(405)
            ->withBody($psr17Factory->createStream($body));
    }
}
