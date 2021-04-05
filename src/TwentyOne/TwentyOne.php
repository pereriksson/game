<?php

namespace pereriksson\TwentyOne;

use pereriksson\Dice\DiceHand;

const PLAYING = 0;
const FINISHED = 1;
const STOPPED = 3;

class TwentyOne
{
    private $players;
    private $status = PLAYING;
    private $rounds = [];
    private $currentRound;
    private $diceHand;

    public function __construct($numberOfDices, $numberOfFaces)
    {
        $this->diceHand = new DiceHand($numberOfDices, $numberOfFaces);
    }

    public function addPlayer($name)
    {
        $this->players[] = new Player($this->diceHand, $name);
    }

    public function newRound()
    {
        $this->currentRound = new Round();
        $this->status = PLAYING;

        foreach ($this->players as $player) {
            $player->setScore(0);
            $player->setStatus(PLAYING);
        }
    }

    public function throwDices(int $player)
    {
        if (!$this->currentRound) {
            return false;
        }

        // TODO: Move playing status to the Round class?
        if ($this->status !== PLAYING) {
            return false;
        }

        if ($this->players[$player]->getStatus() !== PLAYING) {
            return false;
        }

        $this->players[$player]->throwDices();

        if ($this->players[$player]->getScore() == 21) {
            $this->players[$player]->setStatus(STOPPED);
        }

        if ($this->players[$player]->getScore() > 21) {
            $this->players[$player]->setStatus(STOPPED);
        }

        $this->finishGame();
    }

    public function setPlayedAsStopped($player)
    {
        if ($this->players[$player]->getStatus() !== PLAYING) {
            return false;
        }

        $this->players[$player]->setStatus(STOPPED);
        $this->finishGame();
    }

    public function finishGame()
    {
        $finishGame = false;

        // Check if someone has 21
        foreach ($this->players as $player) {
            if ($player->getScore() === 21) {
                $this->currentRound->setWinner($player);
                $finishGame = true;
            }
        }

        // Check if all have played
        $settledPlayers = 0;
        foreach ($this->players as $player) {
            if ($player->getStatus() !== 0) {
                $settledPlayers += 1;
            }
        }

        // All have played, determine the winner
        if ($settledPlayers === count($this->players)) {
            $winner = null;
            $winnerScore = null;
            foreach ($this->players as $player) {
                // Only take stopped players into consideration (no losers)
                if ($player->getScore() <= 21) {
                    if (!$winner) {
                        $winner = $player;
                        $winnerScore = 21 - $player->getScore();
                    }

                    if ($winner && 21 - $player->getScore() < $winnerScore) {
                        $winner = $player;
                        $winnerScore = 21 - $player->getScore();
                    }
                }
            }

            $this->currentRound->setWinner($winner);
            $finishGame = true;
        }

        if ($finishGame) {
            $this->status = FINISHED;
            $this->rounds[] = $this->currentRound;
        }
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    public function getCurrentRound()
    {
        return $this->currentRound;
    }

    /**
     * @return array
     */
    public function getRounds(): array
    {
        return $this->rounds;
    }

    public function getPlayers()
    {
        return $this->players;
    }

    public function resetScore()
    {
        $this->newRound();
        $this->rounds = [];

        foreach ($this->players as $player) {
            $player->setStatus(PLAYING);
            $player->resetScore();
        }
    }
}
