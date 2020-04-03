<?php

namespace App\Services\TicTacToe;

class TicTacToeException extends \Exception
{
    /**
     * @param string $board
     * @param \Exception|null $previous
     *
     * @return TicTacToeException
     */
    public static function invalidBoard(string $board, \Exception $previous = null) : TicTacToeException
    {
        return new self('Following board "' . $board . '" has invalid structure.', 0, $previous);
    }

    /**
     * @param string $message
     * @param \Exception|null $previous
     *
     * @return TicTacToeException
     */
    public static function invalidMove(string $message, \Exception $previous = null) : TicTacToeException
    {
        return new self('[Invalid move] ' . $message, 0, $previous);
    }

}