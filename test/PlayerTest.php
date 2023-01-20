<?php

use App\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    private Player $player;

    protected function setUp(): void
    {
        parent::setUp();

        $this->player = new Player('Malyom');
    }

    /** @test */
    public function construct_PlayerCreatedWithDefaultProperties()
    {
        $name = 'Ló';
        $player = new Player($name);

        $this->assertEquals($name, $player->getName());
        $this->assertEquals(0, $player->getPosition());
    }

    /** @test */
    public function advance_StayInCurrentLap_StepsAddedToPosition()
    {
        $this->player->advance(3);

        $this->assertEquals(3, $this->player->getPosition());
    }

    /** @test */
    public function advance_CrossLapBoundary_PositionRollsOver()
    {
        $this->player->advance(8);
        $this->player->advance(5);

        $this->assertEquals(1, $this->player->getPosition());
    }
}
