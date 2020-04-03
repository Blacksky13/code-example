<?php

namespace App\Repositories;

use App\Models\Game;
use Psr\Container\ContainerInterface;

use function uuid;

class GameRepository
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $dir;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $dir = $container->get('base_path') . '/storage/games';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->dir = $dir;
    }

    /**
     * @return Game[]|array
     */
    public function get() : array
    {
        $items = [];
        foreach(glob($this->dir . '/*') as $file) {
            $data = json_decode(file_get_contents($file), true);
            $game = (new Game)
                ->setId($data['id'])
                ->setBoard($data['board'])
                ->setStatus($data['status']);
            $items[] = $game;
        }

        return $items;
    }

    /**
     * @param string $id
     *
     * @return Game|null
     */
    public function find(string $id) : ?Game
    {
        try {
            $data = json_decode(file_get_contents($this->dir . '/' . $id), true);

            $game = (new Game())
                ->setId($data['id'])
                ->setBoard($data['board'])
                ->setStatus($data['status']);
        } catch (\Exception $e) {
            return null;
        }

        return $game;
    }

    /**
     * @param array $data
     *
     * @return Game
     * @throws \Exception
     */
    public function create(array $data = []) : Game
    {
        $game = (new Game())->setId(uuid())
            ->setBoard($data['board'])
            ->setStatus(Game::STATUS_RUNNING);

        if ($this->save($game) === false) {
            throw new \Exception('Game can\'t be created.');
        }

        return $game;
    }

    /**
     * @param Game $game
     *
     * @return bool
     */
    public function save(Game $game) : bool
    {
        $path = $this->dir . '/' . $game->getId();

        try {
            file_put_contents($path, json_encode($game->toArray()));
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param Game $game
     *
     * @return bool
     */
    public function delete(Game $game) : bool
    {
        try {
            unlink($this->dir . '/' . $game->getId());
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}