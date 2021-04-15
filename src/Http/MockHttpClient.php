<?php

namespace pereriksson\Http;

class MockHttpClient implements HttpInterface
{
    private $get;
    private $post;
    private $server;

    public function __construct()
    {
        $this->get = [];
        $this->post = [];
        $this->server = [
            "SERVER_PROTOCOL" => "https",
            "SCRIPT_NAME" => "index.php",
            "SERVER_NAME" => "example.com",
            "SERVER_PORT" => "80",
            "REQUEST_URI" => "https://example.com"
        ];
    }

    public function getAllGet()
    {
        return $this->get;
    }

    public function getAllPost()
    {
        return $this->post;
    }

    public function getAllServer()
    {
        return $this->server;
    }

    public function setGet($name, $value)
    {
        $this->get[$name] = $value;
    }

    public function setPost($name, $value)
    {
        $this->post[$name] = $value;
    }
}
