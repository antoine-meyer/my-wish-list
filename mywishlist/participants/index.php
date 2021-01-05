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
    $rs->getBody()->write("
    <!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <title>Application Wishlist</title>
        <link rel='stylesheet' href='../web/css/style.css'>
    </head>
    <body>
        <h1>Application Wishlist</h1>
        <h2><u>Participants</u></h2>
        <p>Bienvenue sur l'application wishlist</p>
        <p>Vous ne voyez rien ici car vous netes pas sur une bonne url de liste de souhaits</p>
        <p>Verifiez dans la barre en haut si vous avez la bonne url</p>
        <p>l'url devrait ressembler à : /mywishlist/participants/liste...</p>
    </body>
    </html>
    ");
    return $rs;
});

$app->get('/liste', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerParticipant($this);
    return $c->getListeDestinataire($rq, $rs, $args);
});

$app->get('/items/{id}', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerParticipant($this);
    return $c->getItem($rq, $rs, $args);
});

$app->post('/traitementFormulaireMessageListe', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerParticipant($this);
    return $c->postFormulaireMessageListe($rq, $rs, $args);
});



$app->run();