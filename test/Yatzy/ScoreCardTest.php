<?php

declare(strict_types=1);

namespace pereriksson\Yatzy;

use pereriksson\Dice\DiceHand;
use pereriksson\Player\Player;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for the controller Debug.
 */
class ScoreCardTest extends TestCase
{
    public function testCreateScoreCard()
    {
        $diceHand = new DiceHand(2, 6);
        $player = new Player($diceHand, "John");
        $sc = new ScoreCard($player);
        $this->assertEquals($player, $sc->getPlayer());
    }

    public function testNumbers()
    {
        $diceHand = new DiceHand(2, 6);
        $player = new Player($diceHand, "John");
        $sc = new ScoreCard($player);
        $sc->setOnes(5);
        $sc->setTwos(5);
        $sc->setThrees(5);
        $sc->setFours(5);
        $sc->setFives(5);
        $sc->setSixes(5);
        $this->assertEquals(30, $sc->getSum());
    }
}
