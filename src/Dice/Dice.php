<?php

namespace pereriksson\Dice;

class Dice
{
    public $numberOfFaces;
    private $value;

    public function __construct($numberOfFaces)
    {
        $this->numberOfFaces = $numberOfFaces;
    }

    public function throw()
    {
        $this->value = rand(1, $this->numberOfFaces);
    }

    public function getValue()
    {
        return $this->value;
    }
}
