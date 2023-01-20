<?php

namespace App;

class Player
{
    private string $name;
    private int $position;
    private int $index;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->position = 0;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function advance(int $steps): void
    {
        $this->position += $steps;
        if ($this->position >= Game::LAP_LENGTH) {
            $this->position -= Game::LAP_LENGTH;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function setIndex(int $index): void
    {
        $this->index = $index;
    }
}