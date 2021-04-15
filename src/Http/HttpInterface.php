<?php

namespace pereriksson\Http;

interface HttpInterface
{
    public function getAllGet();

    public function getAllPost();

    public function getAllServer();
}
