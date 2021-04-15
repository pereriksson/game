<?php

declare(strict_types=1);

namespace pereriksson\TwentyOne;

use pereriksson\TwentyOne\TwentyOne;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for the controller Debug.
 */
class TwentyOneTest extends TestCase
{
    public function testCreateTwentyOne()
    {
        $to = new TwentyOne(2, 6);
        $to->addPlayer("John");
        $to->addPlayer("Jane");
        $to->newRound();
        $this->assertEquals(2, count($to->getPlayers()));
    }

    public function testThrow()
    {
        $to = new TwentyOne(2, 6);
        $to->addPlayer("John");
        $to->addPlayer("Jane");
        $to->newRound();
        $to->throwDices(0);
        $this->assertGreaterThanOrEqual(1, $to->getScoreCard()->getScore(0));
    }
}
