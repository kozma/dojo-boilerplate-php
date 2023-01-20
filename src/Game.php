<?php

namespace App;

use App\Logging\Logger;
use InvalidArgumentException;

class Game
{
    public const LAP_LENGTH = 12;
    var $players;
    var $places;
    var $purses;
    var $inPenaltyBox;

    var $popQuestions;
    var $scienceQuestions;
    var $sportsQuestions;
    var $rockQuestions;

    var $currentPlayer = 0;
    var $isGettingOutOfPenaltyBox;
    private Logger $logger;

    function __construct(Logger $logger)
    {
        $this->logger = $logger;

        $this->players = [];
        $this->places = [0];
        $this->purses = [0];
        $this->inPenaltyBox = [0];

        $this->popQuestions = [];
        $this->scienceQuestions = [];
        $this->sportsQuestions = [];
        $this->rockQuestions = [];

        for ($i = 0; $i < 50; $i++) {
            array_push($this->popQuestions, "Pop Question " . $i);
            array_push($this->scienceQuestions, ("Science Question " . $i));
            array_push($this->sportsQuestions, ("Sports Question " . $i));
            array_push($this->rockQuestions, $this->createRockQuestion($i));
        }
    }

    function createRockQuestion($index)
    {
        return "Rock Question " . $index;
    }

    function isPlayable()
    {
        return ($this->howManyPlayers() >= 2);
    }

    function add($playerName)
    {
        array_push($this->players, $playerName);
        $this->places[$this->howManyPlayers()] = 0;
        $this->purses[$this->howManyPlayers()] = 0;
        $this->inPenaltyBox[$this->howManyPlayers()] = false;

        $this->logger->log($playerName . " was added");
        $this->logger->log("They are player number " . count($this->players));
        return true;
    }

    function howManyPlayers()
    {
        return count($this->players);
    }

    function roll($roll)
    {
        $this->logger->log($this->players[$this->currentPlayer] . " is the current player");
        $this->logger->log("They have rolled a " . $roll);

        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($roll % 2 != 0) {
                $this->isGettingOutOfPenaltyBox = true;

                $this->logger->log($this->players[$this->currentPlayer] . " is getting out of the penalty box");
                $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
                if ($this->places[$this->currentPlayer] > 11) {
                    $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;
                }

                $this->logger->log(
                    $this->players[$this->currentPlayer]
                    . "'s new location is "
                    . $this->places[$this->currentPlayer]
                );
                $this->logger->log("The category is " . $this->currentCategory());
                $this->askQuestion();
            } else {
                $this->logger->log($this->players[$this->currentPlayer] . " is not getting out of the penalty box");
                $this->isGettingOutOfPenaltyBox = false;
            }
        } else {
            $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
            if ($this->places[$this->currentPlayer] > 11) {
                $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;
            }

            $this->logger->log(
                $this->players[$this->currentPlayer]
                . "'s new location is "
                . $this->places[$this->currentPlayer]
            );
            $this->logger->log("The category is " . $this->currentCategory());
            $this->askQuestion();
        }
    }

    function askQuestion()
    {
        if ($this->currentCategory() == "Pop") {
            $this->logger->log(array_shift($this->popQuestions));
        }
        if ($this->currentCategory() == "Science") {
            $this->logger->log(array_shift($this->scienceQuestions));
        }
        if ($this->currentCategory() == "Sports") {
            $this->logger->log(array_shift($this->sportsQuestions));
        }
        if ($this->currentCategory() == "Rock") {
            $this->logger->log(array_shift($this->rockQuestions));
        }
    }

    function currentCategory()
    {
        if ($this->places[$this->currentPlayer] == 0) {
            return "Pop";
        }
        if ($this->places[$this->currentPlayer] == 4) {
            return "Pop";
        }
        if ($this->places[$this->currentPlayer] == 8) {
            return "Pop";
        }
        if ($this->places[$this->currentPlayer] == 1) {
            return "Science";
        }
        if ($this->places[$this->currentPlayer] == 5) {
            return "Science";
        }
        if ($this->places[$this->currentPlayer] == 9) {
            return "Science";
        }
        if ($this->places[$this->currentPlayer] == 2) {
            return "Sports";
        }
        if ($this->places[$this->currentPlayer] == 6) {
            return "Sports";
        }
        if ($this->places[$this->currentPlayer] == 10) {
            return "Sports";
        }
        return "Rock";
    }

    function wasCorrectlyAnswered()
    {
        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($this->isGettingOutOfPenaltyBox) {
                $this->logger->log("Answer was correct!!!!");
                $this->purses[$this->currentPlayer]++;
                $this->logger->log(
                    $this->players[$this->currentPlayer]
                    . " now has "
                    . $this->purses[$this->currentPlayer]
                    . " Gold Coins."
                );

                $winner = $this->didPlayerWin();
                $this->currentPlayer++;
                if ($this->currentPlayer == count($this->players)) {
                    $this->currentPlayer = 0;
                }

                return $winner;
            } else {
                $this->currentPlayer++;
                if ($this->currentPlayer == count($this->players)) {
                    $this->currentPlayer = 0;
                }
                return true;
            }
        } else {
            $this->logger->log("Answer was corrent!!!!");
            $this->purses[$this->currentPlayer]++;
            $this->logger->log(
                $this->players[$this->currentPlayer]
                . " now has "
                . $this->purses[$this->currentPlayer]
                . " Gold Coins."
            );

            $winner = $this->didPlayerWin();
            $this->currentPlayer++;
            if ($this->currentPlayer == count($this->players)) {
                $this->currentPlayer = 0;
            }

            return $winner;
        }
    }

    function wrongAnswer()
    {
        $this->logger->log("Question was incorrectly answered");
        $this->logger->log($this->players[$this->currentPlayer] . " was sent to the penalty box");
        $this->inPenaltyBox[$this->currentPlayer] = true;

        $this->currentPlayer++;
        if ($this->currentPlayer == count($this->players)) {
            $this->currentPlayer = 0;
        }
        return true;
    }

    function didPlayerWin()
    {
        return !($this->purses[$this->currentPlayer] == 6);
    }

    public function getQuestionsForCategory(string $category): array
    {
        return match ($category) {
            'Pop' => $this->popQuestions,
            'Rock' => $this->rockQuestions,
            'Sports' => $this->sportsQuestions,
            'Science' => $this->scienceQuestions,
            default => throw new InvalidArgumentException("Unknown category: '$category'"),
        };
    }
}
