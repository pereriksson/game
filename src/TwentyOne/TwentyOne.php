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
    private $scoreCard;

    public function __construct($numberOfDices, $numberOfFaces)
    {
        $this->diceHand = new DiceHand($numberOfDices, $numberOfFaces);
        $this->scoreCard = new ScoreCard();
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
            $this->scoreCard->resetScore();
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
        $currentScore = $this->scoreCard->getScore($player);
        $this->scoreCard->setScore($player, $currentScore + $this->diceHand->getDiceSum());

        if ($this->scoreCard->getScore($player) == 21) {
            $this->players[$player]->setStatus(STOPPED);
        }

        if ($this->scoreCard->getScore($player) > 21) {
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

    private function playerWithTwentyOneExist(): bool
    {
        foreach ($this->players as $index => $player) {
            if ($this->scoreCard->getScore($index) === 21) {
                $this->currentRound->setWinner($player);
                true;
            }
        }

        return false;
    }

    private function getNumberOfSettledPlayers(): int
    {
        $settledPlayers = 0;

        foreach ($this->players as $player) {
            if ($player->getStatus() !== 0) {
                $settledPlayers += 1;
            }
        }

        return $settledPlayers;
    }

    public function finishGame()
    {
        // Check if someone has 21
        $finishGame = $this->playerWithTwentyOneExist();

        // Check if all have played
        $settledPlayers = $this->getNumberOfSettledPlayers();

        // All have played, determine the winner
        if ($settledPlayers === count($this->players)) {
            $winner = null;
            $winnerScore = null;
            foreach ($this->players as $index => $player) {
                // Only take stopped players into consideration (no losers)
                if ($this->scoreCard->getScore($index) <= 21) {
                    if (!$winner) {
                        $winner = $player;
                        $winnerScore = 21 - $this->scoreCard->getScore($index);
                    }

                    if ($winner && 21 - $this->scoreCard->getScore($index) < $winnerScore) {
                        $winner = $player;
                        $winnerScore = 21 - $this->scoreCard->getScore($index);
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

    public function getPlayerScore(int $player): int
    {
        return $this->scoreCard->getScore($player);
    }

    public function resetScore()
    {
        $this->newRound();
        $this->rounds = [];

        foreach ($this->players as $player) {
            $player->setStatus(PLAYING);
            $this->diceHand->resetHand();
        }
    }
}
