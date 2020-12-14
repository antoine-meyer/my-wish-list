<?php

require_once __DIR__ . "/../src/vendor/autoload.php";

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//
$config = require_once __DIR__ . '/../src/conf/settings.php';
$c = new \Slim\Container($config);
$app = new \Slim\App($c);

//connection à la base de données
\mywishlist\bd\Eloquent::start(__DIR__.'/../src/conf/dbconf.ini');

//
$app->get('[/]', function(Request $rq, Response $rs, array $args): Response {
    $rs->getBody()->write("<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'>
            <title>Application Wishlist</title><link rel='stylesheet' href=\"../web/css/style.css\"></head><body>
            <h1>Application Wishlist COTE CREATEUR</h1>
            <div class='topnav'>
                <a class=\"active\" href=\".\">Home</a>
                <a href=\"./listes\">Listes</a>
                <a href=\"./items\">Items</a>
            </div>
            <br>
            <div class='content'>
            </div>
        </body>
    </html>");
    return $rs;
});

$app->get('/listes', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerParticipant($this);
    return $c->getListes($rq, $rs, $args);
});

$app->get('/items', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerParticipant($this);
    return $c->getItems($rq, $rs, $args);
});

$app->get('/items/{id}', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerParticipant($this);
    return $c->getItem($rq, $rs, $args);
})->setName('item');

$app->get('/listes/{id}', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerParticipant($this);
    return $c->getListe($rq, $rs, $args);
});

$app->run();