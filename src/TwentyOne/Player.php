<?php

namespace pereriksson\TwentyOne;

use pereriksson\Dice\DiceHand;

const PLAYER_PLAYING = 0;

class Player
{
    private $score = 0;
    private $diceHand;
    private $name;
    private $status = PLAYER_PLAYING;

    public function __construct(DiceHand $diceHand, string $name)
    {
        $this->diceHand = $diceHand;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function throwDices()
    {
        $this->diceHand->throwDices();
        $this->score += $this->diceHand->getDiceSum();
    }

    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    public function resetScore()
    {
        $this->score = 0;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }
}
