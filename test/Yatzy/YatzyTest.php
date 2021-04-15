<?php

declare(strict_types=1);

namespace pereriksson\Yatzy;

use pereriksson\Yatzy\Yatzy;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for the controller Debug.
 */
class YatzyTest extends TestCase
{
    public function testCreateYatzy()
    {
        $yatzy = new Yatzy();
        $this->assertInstanceOf("\pereriksson\Dice\DiceHand", $yatzy->getDiceHand());
        $yatzy->addPlayer("John");
        $yatzy->addPlayer("Jane");
        $this->assertEquals(count($yatzy->getPlayers()), 2);
    }
}
