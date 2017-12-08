<?php

use SON\View\ViewRenderer;
use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();

$app['debug'] = true;

$app['view.config'] = [
    'path_templates' => __DIR__ . '/../templates'
];

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => '127.0.0.1',
        'dbname' => 'son_silex_basico',
        'user' => 'root',
        'password' => 'root'
    ),
));

$app['view.renderer'] = function () use ($app) {
    $pathTemplates = $app['view.config']['path_templates'];
    return new ViewRenderer($pathTemplates);
};

$site = include __DIR__ . '/controllers/site.php';
$app->mount('/', $site);

$app->mount('/admin', function($admin) use($app){
    $post = include __DIR__ . '/controllers/posts.php';
    $admin->mount('/posts', $post);
});

$app->error(function(\Exception $e, Request $request, $code) use($app){
    switch ($code){
        case 404:
            return $app['view.renderer']->render('errors/404', [
                'message' => $e->getMessage()
            ]);
    }
});

//dump($app); //use ao inves do var_dump e muito melhor

$app->run();

