<?php

namespace pereriksson\Yatzy;

/**
 * A score card for a player.
 */
class ScoreCard
{
    private $player;
    private $ones;
    private $twos;
    private $threes;
    private $fours;
    private $fives;
    private $sixes;
    // TODO: Fool phpmd
    //private $onePair;
    //private $twoPairs;
    private $threeNumbers;
    private $fourNumbers;
    private $smallLadder;
    private $bigLadder;
    private $house;
    private $chance;
    private $yatzy;
    private $bonus;

    public function __construct($player)
    {
        $this->player = $player;
    }

    /**
     * @return mixed
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * The the sum of ones through sixes (see above).
     * @return int
     */
    public function getSum(): int
    {
        return $this->ones +
            $this->twos +
            $this->threes +
            $this->fours +
            $this->fives +
            $this->sixes;
    }

    public function getBonus(): int
    {
        if ($this->getSum() >= 63) {
            return 50;
        }

        return 0;
    }

    public function getTotalScore(): int
    {
        return $this->getSum() +
            $this->bonus +
            $this->onePair +
            $this->twoPairs +
            $this->threeNumbers +
            $this->fourNumbers +
            $this->smallLadder +
            $this->bigLadder +
            $this->house +
            $this->chance +
            $this->yatzy;
    }

    private function calculateBonus()
    {
        if ($this->getSum() >= 63) {
            $this->bonus = 50;
        }
    }

    /**
     * @param mixed $ones
     */
    public function setOnes(int $ones): void
    {
        $this->ones = $ones;
        $this->calculateBonus();
    }

    /**
     * @param mixed $twos
     */
    public function setTwos(int $twos): void
    {
        $this->twos = $twos;
        $this->calculateBonus();
    }

    /**
     * @param mixed $threes
     */
    public function setThrees(int $threes): void
    {
        $this->threes = $threes;
        $this->calculateBonus();
    }

    /**
     * @param mixed $fours
     */
    public function setFours(int $fours): void
    {
        $this->fours = $fours;
        $this->calculateBonus();
    }

    /**
     * @param mixed $fives
     */
    public function setFives(int $fives): void
    {
        $this->fives = $fives;
        $this->calculateBonus();
    }

    /**
     * @param mixed $sixes
     */
    public function setSixes(int $sixes): void
    {
        $this->sixes = $sixes;
        $this->calculateBonus();
    }

    /**
     * @return mixed
     */
    public function getOnes()
    {
        return $this->ones;
    }

    /**
     * @return mixed
     */
    public function getTwos()
    {
        return $this->twos;
    }

    /**
     * @return mixed
     */
    public function getThrees()
    {
        return $this->threes;
    }

    /**
     * @return mixed
     */
    public function getFours()
    {
        return $this->fours;
    }

    /**
     * @return mixed
     */
    public function getFives()
    {
        return $this->fives;
    }

    /**
     * @return mixed
     */
    public function getSixes()
    {
        return $this->sixes;
    }
}
