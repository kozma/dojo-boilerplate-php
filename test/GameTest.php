<?php

use App\Game;
use App\Logging\NullLogger;
use App\Player;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    private Game $game;

    protected function setUp(): void
    {
        parent::setUp();

        $this->game = new Game(new NullLogger());
    }

    /** @test */
    public function constructor_NoPlayersInGame(): void
    {
        $this->assertEmpty($this->game->players);
    }

    /**
     * @test
     * @dataProvider questionCategories
     */
    public function constructor_QuestionsInitialized(string $category): void
    {
        $questions = $this->game->getQuestionsForCategory($category);
        $this->assertCount(50, $questions);
        $this->assertEquals("$category Question 0", $questions[0]);
        $this->assertEquals("$category Question 49", $questions[49]);
    }

    public function questionCategories(): array
    {
        return [
            'pop' => ['Pop'],
            'science' => ['Science'],
            'sports' => ['Sports'],
            'rock' => ['Rock'],
        ];
    }

    /** @test */
    public function add_EmptyGame_OnePlayerAddedAtPositionZero(): void
    {
        $this->game->add('Pipi');

        $this->assertEquals(1, $this->game->howManyPlayers());
        $this->assertPlayerState('Pipi', 0, 0, 0, false);
    }

    /** @test */
    public function add_SecondPlayerAdded_OnePlayerAddedAtPositionZero(): void
    {
        $this->game->add('Pipi');
        $this->game->add('Maci');

        $this->assertEquals(2, $this->game->howManyPlayers());
        $this->assertPlayerState('Maci', 1, 0, 0, false);
    }

    /**
     * @test
     * @dataProvider positionToCategoryMapping
     */
    public function currentCategory_PlayerPlacesPassed_RoundRobinCategoriesReturned($playerPlace, $expectedCategory)
    {
        $this->game->currentPlayer = 0;
        $this->game->places[$this->game->currentPlayer] = $playerPlace;
        $this->assertEquals($expectedCategory, $this->game->currentCategory());
    }

    public function positionToCategoryMapping(): array
    {
        return [
            [0,'Pop'],
            [1,'Science'],
            [2,'Sports'],
            [3,'Rock'],
            [4,'Pop'],
            [5,'Science'],
            [6,'Sports'],
            [7,'Rock'],
            [8,'Pop'],
            [9,'Science'],
            [10,'Sports'],
            [11,'Rock'],
        ];
    }

    /** @test */
    public function roll_PlayerIsNotInPrisonStaysInTheSameLap_PositionAdvancesByRolledNumber()
    {
        $player = $this->givenAPlayerAtPosition(4);
        $this->givenTheCurrentPlayerIs($player);

        $this->game->roll(3);

        $this->assertEquals(7, $this->game->places[$player->getIndex()]);
    }

    /** @test */
    public function roll_PlayerIsNotInPrisonStaysRollsOverToNextLap_PositionRollsOver()
    {
        $player = $this->givenAPlayerAtPosition(10);
        $this->givenTheCurrentPlayerIs($player);

        $this->game->roll(3);

        $this->assertEquals(1, $this->game->places[$player->getIndex()]);
    }

    /** @test */
    public function roll_PlayerIsNotInPrison_QuestionConsumedFromCategoryOfDestinationSquare()
    {
        $player = $this->givenAPlayerAtPosition(4);
        $this->givenTheCurrentPlayerIs($player);

        $this->game->roll(3);

        $this->assertCount(49, $this->game->getQuestionsForCategory('Rock'));
    }

    private function assertPlayerState(
        string $expectedName,
        int $playerNumber,
        int $expectedPlace,
        int $expectedGolds,
        bool $expectedInPenaltyBox
    ): void {
        $this->assertEquals($expectedName, $this->game->players[$playerNumber]);
        $this->assertEquals($expectedPlace, $this->game->places[$playerNumber]);
        $this->assertEquals($expectedGolds, $this->game->purses[$playerNumber]);
        $this->assertEquals($expectedInPenaltyBox, $this->game->inPenaltyBox[$playerNumber]);
    }

    private function givenAPlayerAtPosition(int $playerPosition): Player
    {
        $player = new Player('Mici');
        $player->advance($playerPosition);
        $this->game->add($player->getName());
        $player->setIndex($this->game->howManyPlayers() - 1);
        $this->game->places[$player->getIndex()] = $player->getPosition();
        return $player;
    }

    private function givenTheCurrentPlayerIs(Player $player)
    {
        $this->game->currentPlayer = $player->getIndex();
    }
}
