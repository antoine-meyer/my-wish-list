<?php

namespace mywishlist\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \mywishlist\models\Liste as Liste;
use \mywishlist\models\Compte as Compte;
use \mywishlist\view\ViewErreur as ViewErreur;
use \Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;

class ControllerCreateur {

    private $c;

    public function __construct(\Slim\Container $c){
        $this->c = $c;
    }

    public function affiche_page_erreur($rq, $rs, $args){
        $htmlvars = ['basepath'=>$rq->getUri()->getBasePath()];
        $v = new ViewErreur(NULL); 
        $rs->getBody()->write($v->render($htmlvars));
        return $rs;
    }

    public function getCompteCreateur($rq, $rs, $args){
        try{
            //sur cette page on va afficher
            //toutes les listes de l'user {id passe}


            //on recupere le chemin de base
            $htmlvars = ['basepath'=>$rq->getUri()->getBasePath()];
            //on recupere rien ou la ou les listes de ce compte
            $compte = Compte::where('id','=', $args['userid'])->firstOrFail();
            $liste = $compte->listes()->get();
            
            //FAIRE UNE VUE
            //BOULOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOT ICI
            /*
            $a = count($liste);
            $html = <<<END
                <p>Vous etes {$compte->nom}</p>
                <p>Votre mdp est {$compte->password}</p>
                <p>Vous avez {$a} liste(s).</p>
                <p></p>
                <p></p>
            END;
            */

            $rs->getBody()->write($html);
           
        }catch(ModelNotFoundException $e){
            $rs = $this->affiche_page_erreur($rq, $rs, $args);
        }
        return $rs;
    }

}