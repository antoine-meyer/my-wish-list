<?php

namespace mywishlist\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \mywishlist\models\Item as Item;
use \mywishlist\models\Liste as Liste;
use \mywishlist\models\Compte as Compte;
use \mywishlist\view\ViewErreur as ViewErreur;
use \mywishlist\view\ViewAccueil as ViewAccueil;
use \mywishlist\view\ViewCreateur as ViewCreateur;
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

    public function problemeConnectionAccueil($rq, $rs, $args){
        //on affiche message erreur
        $rs->getBody()->write("<p style='color:red;'>Mauvais identifiant et/ou mot de passe.</p>");
        //on récupère la vue de l'accueil normal
        $rs = $this->testAccueil($rq, $rs, $args);
        return $rs;
    }

    public function postConnectionAccueil($rq, $rs, $args){
        //formulaire pour esayer de se connecter
        if($rq->getParsedBody()['bouton_connectionCreateur'] === ""){
            $rs = $this->effectueConnection($rq, $rs, $args);
        }
        //renvoie la vue
        return $rs;
    }

    public function effectueConnection($rq, $rs, $args){
        try{
            //on récupère les champs
            $ide = $rq->getParsedBody()['identifiant'];
            $mdp = $rq->getParsedBody()['mot_de_passe'];
            //on filtre les informations
            $ideFiltre = filter_var($ide, FILTER_SANITIZE_STRING);
            $mdpFiltre = filter_var($mdp, FILTER_SANITIZE_STRING);

            //on regarde si le identifiant existe sinon problème
            $compte = Compte::where('nom','=', $ideFiltre)->firstOrFail();
            
            //on regarde si le mot de passe entré correspond à ce compte
            if($compte->password === $mdpFiltre){
                //bon mot de passe !
                //on crée une session
                session_start();
                //on met le nom du compte dans une variable de session
                $_SESSION['id_compte_actif'] = $compte->id;
                //on récupère l'id du compte
                $i = $compte->id;
                //on récupère le token du compte
                $t = $compte->tokenmodification;
                //on retourne la nouvelle URL vers laquelle on veut aller
                return $rs->withRedirect("/mywishlist/createurs/compte/{$i}?token={$t}");
            }else{
                //pas le bon mdp donc on fait erreur
                $rs = $this->problemeConnectionAccueil($rq, $rs, $args);
            }   
        }catch(ModelNotFoundException $e){
            //renvoie de la vue
            $rs = $this->problemeConnectionAccueil($rq, $rs, $args);
        }
        return $rs;
    }

}