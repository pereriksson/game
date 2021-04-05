<?php

declare(strict_types=1);

namespace Mos\Router;

use pereriksson\TwentyOne\TwentyOne;
use pereriksson\Util\Util;

const PLAYING = 0;
const FINISHED = 1;
const WON = 1;
const LOST = 2;

function simulateComputer($twentyone)
{
    $computer = $twentyone->getPlayers()[1];

    //while (true) {
    for ($i = 0; $i < 20; $i++) {
        $twentyone->throwDices(1);

        if ($computer->getStatus() !== PLAYING) {
            break;
        }

        if (21 - $computer->getScore() <= 5) {
            $twentyone->setPlayedAsStopped(1);
            break;
        }
    }
}

/**
 * Class Router.
 */
class Router
{
    public static function dispatch(string $method, string $path): void
    {
        if ($method === "GET" && $path === "/") {
            $data = [
                "title" => "Home",
                "component" => "components/home.html"
            ];
        } else if ($method === "GET" && $path === "/session") {
            $data = [
                "title" => "Home",
                "session" => $_SESSION,
                "component" => "components/session.html"
            ];
        } else if ($method === "POST" && $path === "/session") {
            Util::destroySession();
            Util::redirectTo(Util::url("/session"));
            return;
        } else if (in_array($method, ["GET", "POST"]) && $path === "/twentyone") {
            if (isset($_POST["action"]) && $_POST["action"] == "leave") {
                unset($_SESSION["twentyone"]);
            }

            if (isset($_POST["action"]) && $_POST["action"] == "start") {
                $_SESSION["twentyone"] = new TwentyOne($_POST["number_of_dices"], 6);
                $_SESSION["twentyone"]->addPlayer("Jag");
                $_SESSION["twentyone"]->addPlayer("Dator");
                $_SESSION["twentyone"]->newRound();
            }

            if (isset($_SESSION["twentyone"])) {
                $twentyone = $_SESSION["twentyone"];
                $me = $twentyone->getPlayers()[0];
                $computer = $twentyone->getPlayers()[1];

                if (isset($_POST["action"]) && $_POST["action"] == "reset") {
                    $_SESSION["twentyone"]->resetScore();
                }

                if (isset($_POST["action"]) && $_POST["action"] == "throw") {
                    $twentyone->throwDices(0);

                    if ($me->getStatus() !== PLAYING) {
                        simulateComputer($twentyone);
                    }
                }

                if (isset($_POST["action"]) && $_POST["action"] == "new_round") {
                    $twentyone->newRound();
                }

                if (isset($_POST["action"]) && $_POST["action"] == "stop") {
                    $twentyone->setPlayedAsStopped(0);

                    simulateComputer($twentyone);
                }


                $data = [
                    "component" => "twntyone.html",
                    "title" => "Tjugoett",
                    "twentyone" => $_SESSION["twentyone"],
                    "my_score" => $me->getScore(),
                    "computer_score" => $computer->getScore(),
                    "status" => $twentyone->getStatus() === PLAYING ? "playing" : "finished",
                    "rounds" => $twentyone->getRounds()

                ];

                if ($twentyone->getStatus() === FINISHED) {
                    if ($twentyone->getCurrentRound()->getWinner()) {
                        $data["winner"] = $twentyone->getCurrentRound()->getWinner()->getName();
                    } else {
                        $data["winner"] = "Ingen";
                    }
                }
            }

            $data["component"] = "components/twentyone.html";
        }

        $data["navItems"] = [
            [
                "url" => Util::url("/"),
                "label" => "Home"
            ],
            [
                "url" => Util::url("/twentyone"),
                "label" => "Game 21"
            ],
            [
                "url" => Util::url("/session"),
                "label" => "Session"
            ]
        ];

        $body = Util::renderTwigView("index.html", $data);
        Util::sendResponse($body, 404);
    }
}
