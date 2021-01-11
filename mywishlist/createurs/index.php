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
/*
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
        <h2><u>Créateurs</u></h2>
        <h1>CE FORMULAIRE NE SERT ENCORE A RIEN; MET /compte/4?tokenmodif=.... pour voir quelque chose !</h1>
        <form method='get' class='formuCrea' action=''>
            <div class=''>
                <div class=''>
                    <input class='cham' title='E-mail ou identifiant' maxlength='320' type='text' placeholder='E-mail ou identifiant' autocorrect='off' spellcheck='false'>
                </div>
                <div class=''>
                    <input class='cham' title='Mdp' maxlength='320' type='password' placeholder='Mot de passe'>
                </div>
            </div>
            <div class=''>    
                <button class='sub' type='submit'>Se connecter</button>
            </div>
        </form>
    </body>
    </html>
    ");
    return $rs;
});*/

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
$app->post('/compte/{userid}', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerCreateur($this);
    return $c->forCreationNewListe($rq, $rs, $args);
});

$app->post('/liste/{no}', function(Request $rq, Response $rs, array $args): Response {
    $c = new mywishlist\controller\ControllerCreateur($this);
    return $c->formuModificationListe($rq, $rs, $args);
});



$app->run();