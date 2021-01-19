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

//get
$app->get('[/]', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerAccueil($this);
    return $c->testAccueil($rq, $rs, $args);
});

$app->get('/compte/{userid}', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerCreateur($this);
    return $c->getCompteCreateur($rq, $rs, $args);
});

$app->get('/liste/{no}', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerCreateur($this);
    return $c->getListeCreateur($rq, $rs, $args);
});

$app->get('/items/{id}', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerCreateur($this);
    return $c->getItemCreateur($rq, $rs, $args);
});



//post
$app->post('[/]', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerAccueil($this);
    return $c->postConnectionAccueil($rq, $rs, $args);
});

$app->post('/compte/{userid}', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerCreateur($this);
    return $c->postCompte($rq, $rs, $args);
});

$app->post('/liste/{no}', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerCreateur($this);
    return $c->formuModificationListe($rq, $rs, $args);
});

$app->post('/items/{id}', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerCreateur($this);
    return $c->formuModificationItem($rq, $rs, $args);
});



$app->run();