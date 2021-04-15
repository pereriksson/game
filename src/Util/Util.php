<?php

namespace pereriksson\Util;

use pereriksson\Http\HttpInterface;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use pereriksson\Filters\ScoreCardFilter;

class Util
{
    private $http;

    public function __construct(HttpInterface $http)
    {
        $this->http = $http;
    }

    /**
     * Get the route path representing the page being requested.
     *
     * @return string with the route path requested.
     */
    public function getRoutePath(): string
    {
        $offset = strlen(dirname($this->http->getAllServer()["SCRIPT_NAME"]));
        $path = substr($this->http->getAllServer()["REQUEST_URI"], $offset);

        return $path;
    }


    /**
     * Use Twig to render a view and return its rendered content.
     *
     * @param string $template to use when rendering the view
     * @param array $data send to as variables to the view
     * @return string with the route path requested
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderTwigView(string $template, array $data = []): string
    {
        static $loader = null;
        static $twig = null;

        if (is_null($twig)) {
            $loader = new FilesystemLoader(
                INSTALL_PATH . "/view"
            );

            $twig = new Environment($loader, [
                'debug' => true,
            ]);

            $twig->addExtension(new DebugExtension());
            $twig->addExtension(new ScoreCardFilter());
        }

        return $twig->render($template, $data);
    }


    /**
     * Send a response to the client.
     *
     * @param int $status HTTP status code to send to client.
     *
     * @return void
     */
    public function sendResponse(string $body, int $status = 200): void
    {
        http_response_code($status);
        echo $body;
    }


    /**
     * Redirect to an url.
     *
     * @param string $url where to redirect.
     *
     * @return void
     */
    public function redirectTo(string $url): void
    {
        http_response_code(200);
        header("Location: $url");
    }


    /**
     * Create an url into the website using the path and prepend the baseurl
     * to the current website.
     *
     * @param string $path to use to create the url.
     *
     * @return string with the route path requested.
     */
    public function url(string $path): string
    {
        return $this->getBaseUrl() . $path;
    }


    /**
     * Get the base url from the request, relative to the htdoc/ directory.
     *
     * @return string as the base url.
     */
    public function getBaseUrl()
    {
        static $baseUrl = null;

        if ($baseUrl) {
            return $baseUrl;
        }

        $scriptName = rawurldecode($this->http->getAllServer()["SCRIPT_NAME"]);
        $path = rtrim(dirname($scriptName), "/");

        // Prepare to create baseUrl by using currentUrl
        $parts = parse_url($this->getCurrentUrl());

        // Build the base url from its parts
        $siteUrl = "{$parts["scheme"]}://{$parts["host"]}"
            . (isset($parts["port"])
                ? ":{$parts["port"]}"
                : "");
        $baseUrl = $siteUrl . $path;

        return $baseUrl;
    }


    /**
     * Get the current url of the request.
     *
     * @return string as current url.
     */
    public function getCurrentUrl(): string
    {
        $scheme = stripos($this->http->getAllServer()['SERVER_PROTOCOL'], 'https') === 0 ? 'https' : 'http';
        $server = $this->http->getAllServer()["SERVER_NAME"];

        $port = $this->http->getAllServer()["SERVER_PORT"];
        $port = ($port === "80")
            ? ""
            : (($port === 443 && $this->http->getAllServer()["HTTPS"] === "on")
                ? ""
                : ":" . $port);

        $uri = rtrim(rawurldecode($this->http->getAllServer()["REQUEST_URI"]), "/");

        $url = htmlspecialchars($scheme) . "://";
        $url .= htmlspecialchars($server)
            . $port . htmlspecialchars(rawurldecode($uri));

        return $url;
    }
}
