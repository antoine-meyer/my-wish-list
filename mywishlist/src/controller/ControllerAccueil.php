<?php

namespace mywishlist\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \mywishlist\models\Item as Item;
use \mywishlist\models\Liste as Liste;
use \mywishlist\models\Compte as Compte;
use \mywishlist\view\ViewErreur as ViewErreur;
use \mywishlist\view\ViewAccueil as ViewAccueil;
use \Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;

class ControllerAccueil {

    private $c;

    public function __construct(\Slim\Container $c){
        $this->c = $c;
    }

    public function testAccueil($rq, $rs, $args){
        $htmlvars = ['basepath'=>$rq->getUri()->getBasePath()];
        //on récupère que les titres des listes qui sont publiques
        $listes = Liste::all();
        $res = [];
        for($i=0; $i<count($listes); $i++){
            if($listes[$i]->publique === 1){
                //on ajoute cette liste publique aux choses que l'on veut afficher
                array_push($res, $listes[$i]);
            }
        }
        $v = new ViewAccueil([$res]);    
        $rs->getBody()->write($v->renderPageAccueil($htmlvars));
        return $rs;
    }

}