<?php

use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

$container = new DI\Container();

Slim\Factory\AppFactory::setContainer($container);

$app = Slim\Factory\AppFactory::create();

$container->set('settings', function () {
    return [
        'displayErrorDetails' => getenv('APP_DEBUG') === 'true',

        'app' => [
            'name' => getenv('APP_NAME')
        ],

        'views' => [
            'dir' => getenv('VIEW_DIR'),
            'cache' => getenv('VIEW_CACHE_ENABLED') === 'true' ? getenv('VIEW_DIR_CACHE') : false
        ]
    ];
});

// Twig Configuration
// Source: https://github.com/slimphp/Twig-View
// 1.Set view in Container
$container->set('view', function () use ($container) {
    $path = __DIR__.'/../'.getenv('VIEW_DIR');
    return Twig::create(
        $path,
        ['cache' => __DIR__.'/../'.$container->get('settings')['views']['cache']]);
});
// 2.Add Twig-View Middleware
$app->add(TwigMiddleware::createFromContainer($app));

require_once __DIR__ . '/../routes/web.php';

