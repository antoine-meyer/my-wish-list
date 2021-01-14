<?php

namespace mywishlist\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \mywishlist\models\Item as Item;
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


    public function formuModificationItem($rq, $rs, $args){
        //formulaire pour supprimer une image d'un item
        if($rq->getParsedBody()['bouton_supprimerUneImage'] === ""){
            $rs = $this->gestionSupprimerUneImage($rq, $rs, $args);
        }
        //renvoi du résultat final
        return $rs; 
    }


    public function formuModificationListe($rq, $rs, $args){
        //formualire pour ajouter un item
        if($rq->getParsedBody()['bouton_newItem'] === ""){
            //print("nouveau item");
            $rs = $this->gestionAjoutItem($rq, $rs, $args);
        }
        //formulaire pour modifier une liste
        if($rq->getParsedBody()['bouton_modifierListe'] === ""){
            //print("modif liste");
            $rs = $this->gestionModificationInformationsListe($rq, $rs, $args);
        }
        //formulaire pour supprimer un item
        //si le type de la valeur du bouton est string alors c'est ok !
        if(gettype($rq->getParsedBody()['bouton_supprimerItem']) === "string"){
            //print("supprimer item");
            $rs = $this->gestionSupprimerItem($rq, $rs, $args);
        }
        //formulaire pour partager la liste
        if($rq->getParsedBody()['bouton_partagerListe'] === ""){
            //print("partage de la liste ");
            //on récupère la liste
            $modifListe = Liste::where('no','=', $args['no'])->firstOrFail();
            //on lui donne un token généré aléatoirement
            $tok = random_bytes(10);
            $tok = bin2hex($tok);
            $modifListe->token = $tok;
            //on enregistre la modification
            $modifListe->save();
            //on renvoie la vue
            $rs = $this->getListeCreateur($rq, $rs, $args);
        }
        //renvoi du résultat final
        return $rs;       
    }


    public function gestionSupprimerUneImage($rq, $rs, $args){
        //on récupère l'id de l'item
        $a = $args['id'];
        //on récupère l'item
        $item = Item::where('id','=', $a)->firstOrFail();
        //on remplace son champ img par NULL
        $item->img = NULL;
        //on sauvegarde
        $item->save();
        //renvoie de la vue
        $rs = $this->getItemCreateur($rq, $rs, $args);
        return $rs;
    }


    public function gestionSupprimerItem($rq, $rs, $args){
        //numéro de l'item à supprimer de la table
        $numItemASuppr = $rq->getParsedBody()['bouton_supprimerItem']; 
        //print($numItemASuppr);
        //on récupère l'item
        $item = Item::where('id','=', $numItemASuppr)->firstOrFail();
        //print($item);
        //on supprimer l'item de la table
        $item->delete();
        //renvoie de la vue
        $rs = $this->getListeCreateur($rq, $rs, $args);
        return $rs;
    }


    public function gestionAjoutItem($rq, $rs, $args){
        //on recupère les données
        $liste_id = $args['no'];
        $nom = $rq->getParsedBody()['newItem_Nom'];
        $des = $rq->getParsedBody()['newItem_Description'];
        $tarif = $rq->getParsedBody()['newItem_Prix']; 
        $url = $rq->getParsedBody()['newItem_URL'];
        $reserve = 0;

        //filtres
        $nomFiltre = filter_var($nom, FILTER_SANITIZE_STRING);
        $desFiltre = filter_var($des, FILTER_SANITIZE_STRING);
        $urlFiltre = filter_var($url, FILTER_SANITIZE_STRING);

        //insertions
        $newItem = new \mywishlist\models\Item;
        $newItem->liste_id = $liste_id;
        $newItem->nom = $nomFiltre;
        $newItem->descr = $desFiltre;
        $newItem->tarif = $tarif;
        $newItem->reserve = $reserve;
        if($urlFiltre!== ""){
            $newItem->url = $urlFiltre;
        }
        $newItem->save();

        //renvoie de la vue
        $rs = $this->getListeCreateur($rq, $rs, $args);
        return $rs;
    }
    

    public function gestionModificationInformationsListe($rq, $rs, $args){
        try{
            //on recupère les données
            $titre = $rq->getParsedBody()['title'];
            $des = $rq->getParsedBody()['description'];
            $dat = $rq->getParsedBody()['date'];
            //si tous les champs sont vides on renvoie la vue car rien a faire
            if($titre === "" && $des === "" && $dat === ""){
                //on renvoie la vue de la même chose ...
                $rs = $this->getListeCreateur($rq, $rs, $args);
            }else{
                //on doit modifier quelque chose au moins OU plusieurs 
                $modifListe = Liste::where('no','=', $args['no'])->firstOrFail();
                $t = $rq->getQueryParam('token', null);
                //on regarde si le token est bon sinon erreur
                if($t === $modifListe->tokenDeModification){
                    //le titre
                    if($titre !== ""){
                        //filtres
                        $titreFiltre = filter_var($titre, FILTER_SANITIZE_STRING);
                        //insertion
                        $modifListe->titre = $titreFiltre;
                    }
                    //la description
                    if($des !== ""){
                        //filtres
                        $descrFiltre = filter_var($des, FILTER_SANITIZE_STRING);
                        //insertion
                        $modifListe->description = $descrFiltre;
                    }
                    //la date
                    if($dat !== ""){
                        //insertion
                        $modifListe->expiration = $dat;
                    }
                    //sauvegarde
                    $modifListe->save();
                    //renvoie la vue
                    $rs = $this->getListeCreateur($rq, $rs, $args);
                }else{
                    //erreur si mauvais token
                    $rs = $this->affiche_page_erreur($rq, $rs, $args);
                }
            }
        }catch(ModelNotFoundException $e){
            $rs = $this->affiche_page_erreur($rq, $rs, $args);
        }
        return $rs;
    }


    public function getListeCreateur($rq, $rs, $args){
        try{
            //on recupere le chemin de base
            $htmlvars = ['basepath'=>$rq->getUri()->getBasePath()];
            //on recupere le token de modification
            $t = $rq->getQueryParam('token', null);
            //on recupere une liste
            $liste = Liste::where('no','=', $args['no'])->firstOrFail();
            //on regarde si le numéro de token et le token de liste correspond
            if($liste->tokenDeModification === $t){
                //on renvoie la vue
                $v = new ViewCreateur([$liste]);    
                $rs->getBody()->write($v->renderUneListe($htmlvars));
            }else{
                //sinon erreur
                $rs = $this->affiche_page_erreur($rq, $rs, $args);
            }
        }catch(ModelNotFoundException $e){
            $rs = $this->affiche_page_erreur($rq, $rs, $args);
        }
        return $rs;
    }





    public function getItemCreateur($rq, $rs, $args){
        try{
            //on recupere le chemin de base
            $htmlvars = ['basepath'=>$rq->getUri()->getBasePath()];
            //on recupere le token de modification
            $t = $rq->getQueryParam('token', null);
            //on recupere un item
            $item = Item::where('id','=', $args['id'])->firstOrFail();
            //on récupère la liste associée
            $liste = $item->liste()->get()[0];
            //on regarde si le token en paramètre vaut la tokenModification de la liste associée à l'item
            if($liste->tokenDeModification === $t){
                //on renvoie la vue
                $v = new ViewCreateur([$item]);    
                $rs->getBody()->write($v->renderUnItem($htmlvars));
            }else{
                //sinon erreur
                $rs = $this->affiche_page_erreur($rq, $rs, $args);
            }
        }catch(ModelNotFoundException $e){
            $rs = $this->affiche_page_erreur($rq, $rs, $args);
        }
        return $rs;
    }





}