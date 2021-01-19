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
        //formulaire pour créer un compte
        if($rq->getParsedBody()['bouton_creationDeCompte'] === ""){
            $rs = $this->creerUnCompte($rq, $rs, $args);
        }
        //renvoie la vue
        return $rs;
    }


    public function creerUnCompte($rq, $rs, $args){
        //on récupère les champs
        $ide = $rq->getParsedBody()['newIden'];
        $mdp = $rq->getParsedBody()['newMDP'];
        $mdpConfir = $rq->getParsedBody()['newMDP_confirmation'];
        //filtres
        $ide = filter_var($ide, FILTER_SANITIZE_STRING);
        $mdp = filter_var($mdp, FILTER_SANITIZE_STRING);
        $mdpConfir = filter_var($mdpConfir, FILTER_SANITIZE_STRING);

        //on regarde en dur si les deux champs entres sont les memes
        if($mdp === $mdpConfir){
            //on génère le hash
            $hash=password_hash($mdp, PASSWORD_DEFAULT);
            //on crée un compte
            $c = new \mywishlist\models\Compte;
            $c->nom = $ide;
            $c->hash = $hash;
            //on génère un token de modification
            $tok = random_bytes(10);
            $tok = bin2hex($tok);
            $c->tokenmodification = $tok;
            //on sauvegarde
            $c->save();
            //on renvoie la vue
            $rs = $this->testAccueil($rq, $rs, $args);
        }else{
            //pas les memes mdp donc message erreur
            $rs->getBody()->write("<p style='color:red;'>Problème création : pas les mêmes mots de passe entrés.</p>");
            //on récupère la vue de l'accueil normal
            $rs = $this->testAccueil($rq, $rs, $args);
        }
        //on renvoi la vue
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
            if( password_verify($mdpFiltre, $compte->hash) ){
                //bon mot de passe !
                //on crée une session
                //session_start();
                //on met le nom du compte dans une variable de session
                //$_SESSION['id_compte_actif'] = $compte->id;
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