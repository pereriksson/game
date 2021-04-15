<?php

namespace pereriksson\Http;

class PhpHttpClient implements HttpInterface
{
    public function getAllGet()
    {
        return $_GET;
    }

    public function getAllPost()
    {
        return $_POST;
    }

    public function getAllServer()
    {
        return $_SERVER;
    }
}
