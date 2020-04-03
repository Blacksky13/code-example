<?php

namespace App\Models;

class Game
{
    const STATUS_RUNNING = 'RUNNING';
    const STATUS_X_WON = 'X_WON';
    const STATUS_O_WON = 'O_WON';
    const STATUS_DRAW = 'DRAW';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $board;

    /**
     * @var string
     */
    private $status;

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Game
     */
    public function setId(string $id) : Game
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getBoard() : string
    {
        return $this->board;
    }

    /**
     * @param string $board
     *
     * @return Game
     */
    public function setBoard(string $board) : Game
    {
        $this->board = $board;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus() : string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Game
     */
    public function setStatus(string $status) : Game
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string|null $state
     *
     * @return $this
     */
    public function mapState(?string $state = null) : Game
    {
        switch ($state) {
            case self::STATUS_X_WON:
            case self::STATUS_O_WON:
            case self::STATUS_DRAW:
                $this->setStatus($state);
                break;
            default:
                $this->setStatus(self::STATUS_RUNNING);
                break;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isFinished() : bool
    {
        return $this->status !== self::STATUS_RUNNING;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return [
            'id' => $this->getId(),
            'board' => $this->getBoard(),
            'status' => $this->getStatus()
        ];
    }
}