<?php

declare(strict_types=1);

namespace pereriksson\Controllers;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use pereriksson\Util\Util;
use pereriksson\Session\Session;
use pereriksson\Yatzy\Yatzy;

/**
 * Controller for the index route.
 */
class YatzyController
{
    const PLAYING = 0;
    const FINISHED = 1;
    const WON = 1;
    const LOST = 2;

    private $util;
    private $session;

    public function __construct(Util $util, Session $session)
    {
        $this->util = $util;
        $this->session = $session;
    }

    public function index(): ResponseInterface
    {
        $psr17Factory = new Psr17Factory();

        $data = [];

        $data["component"] = "components/yatzy.twig";

        $data["navItems"] = [
            [
                "url" => $this->util->url("/"),
                "label" => "Home"
            ],
            [
                "url" => $this->util->url("/twentyone"),
                "label" => "Game 21"
            ],
            [
                "url" => $this->util->url("/yatzy"),
                "label" => "Yatzy"
            ],
            [
                "url" => $this->util->url("/session"),
                "label" => "Session"
            ]
        ];

        if ($this->session->keyExist("yatzy")) {
            $yatzy = $this->session->get("yatzy");
            $data["yatzy"] = $yatzy;

            $dices = $yatzy->getDiceHand()->getDices();

            $data["dices"] = [];

            foreach ($dices as $dice) {
                $data["dices"][] = [
                    "id" => $dice->getId(),
                    "value" => $dice->getValue(),
                    "kept" => $dice->getKept()
                ];
            }

            $data["scoreCards"] = $yatzy->getScoreCards();
            $data["throwRound"] = $yatzy->getThrowRound();
        }

        $body = $this->util->renderTwigView("index.twig", $data);

        return $psr17Factory
            ->createResponse(200)
            ->withBody($psr17Factory->createStream($body));
    }

    private function isPostAction(string $action): bool
    {
        if (isset($_POST["action"]) && $_POST["action"] == $action) {
            return true;
        }

        return false;
    }

    private function leave()
    {
        $this->session->remove("yatzy");
    }

    private function start()
    {
        $this->session->set("yatzy", new Yatzy());
        $this->session->get("yatzy")->addPlayer("Jag");
        $this->session->get("yatzy")->addPlayer("Dator");
        $this->session->get("yatzy")->newRound();
        $this->session->set("row", "ones");
    }

    private function nextStep()
    {
        $yatzy = $this->session->get("yatzy");

        foreach ($yatzy->getDiceHand()->getDices() as $dice) {
            $dice->setKept(true);
        }

        $stepMapping = [
            "ones" => [
                "factor" => 1,
                "next" => "twos",
                "method" => "setOnes"
            ],
            "twos" => [
                "factor" => 2,
                "next" => "threes",
                "method" => "setTwos"
            ],
            "threes" => [
                "factor" => 3,
                "next" => "fours",
                "method" => "setThrees"
            ],
            "fours" => [
                "factor" => 4,
                "next" => "fives",
                "method" => "setFours"
            ],
            "fives" => [
                "factor" => 5,
                "next" => "sixes",
                "method" => "setFives"
            ],
            "sixes" => [
                "factor" => 6,
                "next" => "onePair",
                "method" => "setSixes"
            ],
            "onePair" => [
                "factor" => 6,
                "next" => "twoPair",
                "method" => "setOnePair"
            ]
        ];

        $step = $stepMapping[$this->session->get("row")];

        $qty = array_reduce($yatzy->getDiceHand()->getDices(), function ($acc, $dice) use ($step) {
            return $dice->getValue() === $step["factor"] ? $acc + 1 : $acc;
        }, 0);
        call_user_func([$yatzy->getScoreCards()[0], $step["method"]], $step["factor"] * $qty);

        $this->session->set("row", $step["next"]);
    }

    private function throw()
    {
        $yatzy = $this->session->get("yatzy");

        if ($yatzy->getThrowRound() === 3) {
            $yatzy->setThrowRound(0);
        }

        // Reset before first throw
        if ($yatzy->getThrowRound() === 0) {
            $yatzy->getDiceHand()->resetHand();

            foreach ($yatzy->getDiceHand()->getDices() as $dice) {
                $dice->setKept(false);
            }
        }

        // Lock dices
        foreach ($_POST as $key => $value) {
            if (substr($key, 0, 10) == "keep-dice-" && $value === "true") {
                $uniqid = substr($key, 10);
                $yatzy->getDiceHand()->keepDice($uniqid);
            }
        }

        $yatzy->throwDices(0);

        if ($yatzy->getThrowRound() === 3) {
            $this->nextStep();
        }
    }

    public function action(): ResponseInterface
    {
        if ($this->isPostAction("leave")) {
            $this->leave();
        }

        if ($this->isPostAction("start")) {
            $this->start();
        }

        if ($this->session->keyExist("yatzy")) {
            $yatzy = $this->session->get("yatzy");

            if ($this->isPostAction("reset")) {
                $yatzy->resetScore();
                $this->session->set("row", "ones");
            }

            if ($this->isPostAction("throw")) {
                $this->throw();
            }
        }

        return (new Response())
            ->withStatus(301)
            ->withHeader("Location", $this->util->url("/yatzy"));
    }
}
