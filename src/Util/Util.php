<?php

namespace pereriksson\Util;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Extension\DebugExtension;

class Util
{
    /**
     * Get the route path representing the page being requested.
     *
     * @return string with the route path requested.
     */
    public static function getRoutePath(): string
    {
        $offset = strlen(dirname($_SERVER["SCRIPT_NAME"]));
        $path = substr($_SERVER["REQUEST_URI"], $offset);

        return $path;
    }


    /**
     * Use Twig to render a view and return its rendered content.
     *
     * @param string $template to use when rendering the view.
     * @param array $data send to as variables to the view.
     *
     * @return string with the route path requested.
     */
    public static function renderTwigView(string $template, array $data = []): string
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
    public static function sendResponse(string $body, int $status = 200): void
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
    public static function redirectTo(string $url): void
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
    public static function url(string $path): string
    {
        return Util::getBaseUrl() . $path;
    }


    /**
     * Get the base url from the request, relative to the htdoc/ directory.
     *
     * @return string as the base url.
     */
    public static function getBaseUrl()
    {
        static $baseUrl = null;

        if ($baseUrl) {
            return $baseUrl;
        }

        $scriptName = rawurldecode($_SERVER["SCRIPT_NAME"]);
        $path = rtrim(dirname($scriptName), "/");

        // Prepare to create baseUrl by using currentUrl
        $parts = parse_url(Util::getCurrentUrl());

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
    public static function getCurrentUrl(): string
    {
        // Silenced by Per as key doesn't exist
        //$scheme = $_SERVER["REQUEST_SCHEME"];
        $scheme = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === 0 ? 'https' : 'http';
        $server = $_SERVER["SERVER_NAME"];

        $port = $_SERVER["SERVER_PORT"];
        $port = ($port === "80")
            ? ""
            : (($port === 443 && $_SERVER["HTTPS"] === "on")
                ? ""
                : ":" . $port);

        $uri = rtrim(rawurldecode($_SERVER["REQUEST_URI"]), "/");

        $url = htmlspecialchars($scheme) . "://";
        $url .= htmlspecialchars($server)
            . $port . htmlspecialchars(rawurldecode($uri));

        return $url;
    }


    /**
     * Destroy the session.
     *
     * @return void
     */
    public static function destroySession(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }
}