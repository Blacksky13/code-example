<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Register DI container for resolving dependencies.
 */
$container = new \DI\Container();
AppFactory::setContainer($container);

/**
 * Create main application instance.
 */
$app = AppFactory::create();

/**
 * Set resolving logic for custom dependencies.
 */
$router = $app->getRouteCollector();
$container->set('router', $router);

$container->set('base_path', dirname(__DIR__));

$container->set('GameRepository', function (\Psr\Container\ContainerInterface $container) {
    return new \App\Repositories\GameRepository($container);
});

/**
 * Define API routes.
 */
$app->group('/api/v1', function (\Slim\Routing\RouteCollectorProxy $group) use ($app) {
    $group->group('/games', function (\Slim\Routing\RouteCollectorProxy $group) use ($app) {
        $group->get('', \App\Controllers\GameController::class . ':index')->setName('games.index');
        $group->post('', \App\Controllers\GameController::class . ':create')->setName('games.create');

        $group->get('/{id}', \App\Controllers\GameController::class . ':show')->setName('games.show');
        $group->put('/{id}', \App\Controllers\GameController::class . ':update')->setName('games.update');
        $group->delete('/{id}', \App\Controllers\GameController::class . ':delete')->setName('games.delete');
    });
});

$app->run();