<?php

namespace App\Controllers;

use App\Models\Game;
use App\Repositories\GameRepository;
use App\Services\TicTacToe\TicTacToe;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GameController extends BaseController
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ContainerInterface
     */
    protected $gameRepository;

    /**
     * @param ContainerInterface $container
     * @param GameRepository $gameRepository
     */
    public function __construct(ContainerInterface $container, GameRepository $gameRepository)
    {
        $this->container = $container;
        $this->gameRepository = $gameRepository;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function index(Request $request, Response $response, array $args) : Response
    {
        $items = array_map(function (Game $item) {
            return $item->toArray();
        }, $this->gameRepository->get());

        return $this->response($response, $items);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function create(Request $request, Response $response, array $args) : Response
    {
        $requestData = json_decode($request->getBody()->getContents(), true);
        if (empty($requestData['board'])) {
            return $this->responseBadRequest($response, 'Board parameter is required.');
        }

        try {
            $TicTacToe = new TicTacToe();

            $TicTacToe->play($requestData['board']);
        } catch (\Exception $e) {
            return $this->responseBadRequest($response, $e->getMessage());
        }

        try {
            $game = $this->gameRepository->create(['board' => $TicTacToe->getBoard()]);
        } catch (\Exception $e) {
            return $this->responseServerError($response, $e->getMessage());
        }

        $router = $this->container->get('router');
        return $this->response($response, [
            'location' => $request->getUri()->getScheme() . '://'
                . $request->getUri()->getHost()
                . $router->getRouteParser()->urlFor('games.show', ['id' => $game->getId()])
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function show(Request $request, Response $response, array $args) : Response
    {
        $item = $this->gameRepository->find($args['id']);
        if ($item === null) {
            return $this->responseNotFound($response);
        }

        return $this->response($response, $item->toArray());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function update(Request $request, Response $response, array $args) : Response
    {
        $requestData = json_decode($request->getBody()->getContents(), true);
        if (empty($requestData['board'])) {
            return $this->responseBadRequest($response, 'Board parameter is required.');
        }

        $item = $this->gameRepository->find($args['id']);
        if ($item === null) {
            return $this->responseNotFound($response);
        }

        if ($item->isFinished()) {
            return $this->responseBadRequest($response, 'This game is already finished, you can\'t update it anymore.');
        }

        try {
            $TicTacToe = (new TicTacToe())
                ->setLastBoardState($item->getBoard());

            $TicTacToe->play($requestData['board']);
        } catch (\Exception $e) {
            return $this->responseBadRequest($response, $e->getMessage());
        }

        $item->setBoard($TicTacToe->getBoard())
            ->mapState($TicTacToe->getState());

        if ($this->gameRepository->save($item) === false) {
            return $this->responseServerError($response);
        }

        return $this->response($response, $item->toArray());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args) : Response
    {
        $item = $this->gameRepository->find($args['id']);
        if ($item === null) {
            return $this->responseNotFound($response);
        }

        if ($this->gameRepository->delete($item) === false) {
            return $this->responseServerError($response);
        }

        return $this->response($response, [
            'message' => 'Game has been deleted.'
        ]);
    }
}