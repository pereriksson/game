<?php

declare(strict_types=1);

namespace pereriksson\Controllers;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use pereriksson\Util\Util;
use pereriksson\Session\Session;

const PLAYING = 0;
const FINISHED = 1;
const WON = 1;
const LOST = 2;

/**
 * Controller for the index route.
 */
class TwentyOne
{
    private $util;
    private $session;

    public function __construct(Util $util, Session $session)
    {
        $this->util = $util;
        $this->session = $session;
    }

    private function simulateComputer($twentyone)
    {
        $computer = $twentyone->getPlayers()[1];

        //while (true) {
        for ($i = 0; $i < 20; $i++) {
            $twentyone->throwDices(1);

            if ($computer->getStatus() !== PLAYING) {
                break;
            }

            if (21 - $twentyone->getPlayerScore(1) <= 5) {
                $twentyone->setPlayedAsStopped(1);
                break;
            }
        }
    }

    public function index(): ResponseInterface
    {
        $psr17Factory = new Psr17Factory();

        $data = [];

        if (isset($_SESSION["twentyone"])) {
            $twentyone = $_SESSION["twentyone"];

            $data = [
                "title" => "Tjugoett",
                "twentyone" => $_SESSION["twentyone"],
                "my_score" => $twentyone->getPlayerScore(0),
                "computer_score" => $twentyone->getPlayerScore(1),
                "status" => $twentyone->getStatus() === PLAYING ? "playing" : "finished",
                "rounds" => $twentyone->getRounds()

            ];

            if ($twentyone->getStatus() === FINISHED) {
                $data["winner"] = "Ingen";

                if ($twentyone->getCurrentRound()->getWinner()) {
                    $data["winner"] = $twentyone->getCurrentRound()->getWinner()->getName();
                }
            }
        }

        $data["component"] = "components/twentyone.twig";

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

    public function action(): ResponseInterface
    {
        if ($this->isPostAction("leave")) {
            $this->session->remove("twentyone");
        }

        if ($this->isPostAction("start")) {
            $this->session->set("twentyone", new \pereriksson\TwentyOne\TwentyOne($_POST["number_of_dices"], 6));
            $this->session->get("twentyone")->addPlayer("Jag");
            $this->session->get("twentyone")->addPlayer("Dator");
            $this->session->get("twentyone")->newRound();
        }

        if ($this->session->keyExist("twentyone")) {
            $twentyone = $this->session->get("twentyone");
            $human = $twentyone->getPlayers()[0];

            if ($this->isPostAction("reset")) {
                $_SESSION["twentyone"]->resetScore();
            }

            if ($this->isPostAction("throw")) {
                $twentyone->throwDices(0);

                if ($human->getStatus() !== PLAYING) {
                    $this->simulateComputer($twentyone);
                }
            }

            if ($this->isPostAction("new_round")) {
                $twentyone->newRound();
            }

            if ($this->isPostAction("stop")) {
                $twentyone->setPlayedAsStopped(0);

                $this->simulateComputer($twentyone);
            }
        }

        return (new Response())
            ->withStatus(301)
            ->withHeader("Location", $this->util->url("/twentyone"));
    }
}
