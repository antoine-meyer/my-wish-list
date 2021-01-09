<?php

namespace mywishlist\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \mywishlist\models\Liste as Liste;
use \mywishlist\models\Compte as Compte;
use \mywishlist\view\ViewErreur as ViewErreur;
use \mywishlist\view\ViewCreateur as ViewCreateur;
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
            //on recupere le chemin de base
            $htmlvars = ['basepath'=>$rq->getUri()->getBasePath()];
            //on recupere le token de modification
            $t = $rq->getQueryParam('token', null);
            //on recupere rien ou la ou les listes de ce compte
            $compte = Compte::where('id','=', $args['userid'])->firstOrFail();
            //on regarde si le numéro de token et le numéro de compte correspond
            if($compte->tokenmodification === $t){
                //on recupere les bonnes listes
                $liste = $compte->listes()->get();
                //on renvoie la vue
                $v = new ViewCreateur([$compte, $liste]);    
                $rs->getBody()->write($v->renderListes($htmlvars));
            }else{
                //sinon erreur
                $rs = $this->affiche_page_erreur($rq, $rs, $args);
            }
        }catch(ModelNotFoundException $e){
            $rs = $this->affiche_page_erreur($rq, $rs, $args);
        }
        return $rs;
    }


    public function forCreationNewListe($rq, $rs, $args){
        //on recupère les données
        $titre = $rq->getParsedBody()['tit'];
        $descr = $rq->getParsedBody()['des'];
        $date = $rq->getParsedBody()['dat'];
        $user = $args['userid'];
        $tokMod = random_bytes(10);
        $tokMod = bin2hex($tokMod);
    
        //filtres
        $titreFiltre = filter_var($titre, FILTER_SANITIZE_STRING);
        $descrFiltre = filter_var($descr, FILTER_SANITIZE_STRING);
        
        //insertions
        $newListe = new \mywishlist\models\Liste;
        $newListe->user_id = $user;
        $newListe->titre = $titreFiltre;
        $newListe->description = $descrFiltre;
        $newListe->expiration = $date;
        $newListe->tokenDeModification = $tokMod;
        $newListe->save();

        //renvoie de la vue
        $rs = $this->getCompteCreateur($rq, $rs, $args);
        return $rs;
    }

}