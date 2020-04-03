<?php

namespace App\Services\TicTacToe;

class TicTacToe
{
    const SIGN_X = 'X';
    const SIGN_O = 'O';
    const SIGN_FREE = '-';

    const STATE_X_WIN = 'X_WON';
    const STATE_O_WIN = 'O_WON';
    const STATE_DRAW = 'DRAW';

    /**
     * @var string
     */
    private $board;

    /**
     * @var array
     */
    private $boardArray;

    /**
     * @var string|null
     */
    private $lastBoardState;

    /**
     * @var string|null
     */
    private $state;

    /**
     * @return array
     */
    public static function getValidSigns() : array
    {
        return [
            self::SIGN_FREE,
            self::SIGN_X,
            self::SIGN_O,
        ];
    }

    /**
     * @param string $board
     *
     * @return string
     * @throws TicTacToeException
     */
    public static function validateBoard(string $board) : string
    {
        $purifiedBoard = preg_replace('/[^' . implode('\\', self::getValidSigns()) . ']/', '', $board);
        if (strlen($purifiedBoard) !== 9) {
            throw TicTacToeException::invalidBoard($board);
        }

        return $purifiedBoard;
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
     * @return TicTacToe
     */
    public function setBoard(string $board) : TicTacToe
    {
        $this->board = $board;
        $this->boardArray = str_split($board, 3);

        return $this;
    }

    /**
     * @param string $board
     *
     * @return TicTacToe
     */
    public function setLastBoardState(string $board) : TicTacToe
    {
        $this->lastBoardState = $board;

        return $this;
    }

    /**
     * @param string|null $state
     *
     * @return $this
     */
    public function setState(?string $state = null)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getState() : ?string
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getAISign() : string
    {
        return $this->getPlayerSign() === self::SIGN_O ? self::SIGN_X : self::SIGN_O;
    }

    /**
     * @return string
     */
    public function getPlayerSign() : string
    {
        return self::SIGN_X;
    }

    /**
     * @param string|null $board
     *
     * @return void
     * @throws TicTacToeException
     */
    public function play(?string $board = null) : void
    {
        $this->setBoard($board ? self::validateBoard($board) : '---------');
        $this->processPlayerMove();

        $this->checkState();
        if ($this->getState() !== null) {
            return;
        }
        
        $this->playAI();

        $this->checkState();
    }

    /**
     * Make a random move by AI.
     */
    public function playAI()
    {
        $freeTilesPositions = $this->getFreeTilesPositions();
        $randKey = array_rand($freeTilesPositions);

        $this->board = substr_replace($this->board, $this->getAISign(), $freeTilesPositions[$randKey], 1);
    }

    /**
     * Check if there's a winner already and update state accordingly.
     */
    public function checkState()
    {
        $winner = $this->getWinner();
        switch ($winner) {
            case self::SIGN_O:
                $this->setState(self::STATE_O_WIN);
                break;
            case self::SIGN_X:
                $this->setState(self::STATE_X_WIN);
                break;
        }

        if (strpos($this->board, self::SIGN_FREE) === false) {
            $this->setState(self::STATE_DRAW);
        }
    }

    /**
     * Determine winner or return NULL.
     *
     * @return null|string
     */
    public function getWinner() : ?string
    {
        if ($winner = $this->checkHorizontal()) {
            return $winner;
        }

        if ($winner = $this->checkVertical()) {
            return $winner;
        }

        if ($winner = $this->checkDiagonal()) {
            return $winner;
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function checkHorizontal() : ?string
    {
        $winner = null;

        for ($i = 0; $i < 3; $i++) {
            $winner = $this->boardArray[$i][0];

            for ($j = 0; $j < 3; $j++) {
                if ($this->boardArray[$i][$j] !== $winner) {
                    $winner = null;
                    break;
                }
            }

            if ($winner !== null) {
                break;
            }
        }

        return $winner;
    }

    /**
     * @return null|string
     */
    private function checkVertical() : ?string
    {
        $winner = null;

        for ($i = 0; $i < 3; $i++) {
            $winner = $this->boardArray[0][$i];

            for ($j = 0; $j < 3; $j++) {
                if ($this->boardArray[$j][$i] !== $winner) {
                    $winner = null;
                    break;
                }
            }

            if ($winner !== null) {
                break;
            }
        }
        return $winner;
    }

    /**
     * @return null|string
     */
    private function checkDiagonal() : ?string
    {
        $winner = $this->boardArray[0][0];
        for ($i = 0; $i < 3; $i++) {
            if ($this->boardArray[$i][$i] !== $winner) {
                $winner = null;
                break;
            }
        }

        if ($winner === null) {
            $winner = $this->boardArray[0][2];
            for ($i = 0; $i < 3; $i++) {
                if ($this->boardArray[$i][2 - $i] !== $winner) {
                    $winner = null;
                    break;
                }
            }
        }

        return $winner;
    }

    /**
     * @param null|string $board
     *
     * @return array
     */
    private function getFreeTilesPositions(?string $board = null) : array
    {
        $start = 0;
        $freeTilesPositions = [];
        while(($position = strpos(($board ?? $this->board),self::SIGN_FREE, $start)) !== false) {
            $freeTilesPositions[] = $position;
            $start = $position + 1;
        }

        return $freeTilesPositions;
    }

    /**
     * @throws TicTacToeException
     */
    public function processPlayerMove()
    {
        if ($this->board === $this->lastBoardState) {
            throw TicTacToeException::invalidMove('No movements made.');
        }

        $movePosition = 0;
        $sign = self::SIGN_FREE;
        $freeTilesPositions = $this->getFreeTilesPositions($this->lastBoardState);
        foreach ($freeTilesPositions as $position) {
            $sign = substr($this->board,  $position, 1);
            if ($sign === self::SIGN_FREE) {
                continue;
            }

            if ($sign === $this->getPlayerSign()) {
                $movePosition = $position;
                break;
            }

            throw TicTacToeException::invalidMove('Wrong sign used. Correct sign is - ' . $this->getPlayerSign());
        }

        if ($sign === self::SIGN_FREE) {
            throw TicTacToeException::invalidMove('No movements made.');
        }

        $this->setBoard(substr_replace($this->lastBoardState, $this->getPlayerSign(), $movePosition, 1));
    }
}