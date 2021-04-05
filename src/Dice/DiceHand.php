<?php

namespace pereriksson\Dice;

class DiceHand
{
    private $dices = [];

    public function __construct(int $numberOfDices, int $numberOfFaces)
    {
        for ($i = 0; $i < $numberOfDices; $i++) {
            $this->dices[] = new Dice($numberOfFaces);
        }
    }

    public function throwDices()
    {
        foreach ($this->dices as $dice) {
            $dice->throw();
        }
    }

    public function getDiceValues()
    {
        $diceValues = [];

        foreach ($this->dices as $dice) {
            $diceValues[] = $dice->getValue();
        }

        return $diceValues;
    }

    public function getDiceSum()
    {
        $diceSum = 0;

        foreach ($this->dices as $dice) {
            $diceSum += $dice->getValue();
        }

        return $diceSum;
    }

    public function addDice(Dice $dice)
    {
        $this->dices[] = $dice;
    }
}
