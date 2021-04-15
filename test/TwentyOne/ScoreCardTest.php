<?php

declare(strict_types=1);

namespace pereriksson\TwentyOne;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for the controller Debug.
 */
class ScoreCardTest extends TestCase
{
    public function testCreateScoreCard()
    {
        $sc = new ScoreCard();
        $sc->setScore(0, 10);
        $this->assertEquals(10, $sc->getScore(0));
        $this->assertEquals(0, $sc->getScore(1));
        $sc->resetScore();
        $this->assertEquals(0, $sc->getScore(0));
    }
}
