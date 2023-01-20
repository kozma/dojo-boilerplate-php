<?php

use App\Game;
use App\Logging\NullLogger;
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
}
