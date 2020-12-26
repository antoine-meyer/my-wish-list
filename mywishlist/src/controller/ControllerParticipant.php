<?php

namespace mywishlist\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \mywishlist\models\Item as Item;
use \mywishlist\models\Liste as Liste;
use \mywishlist\view\ViewParticipant as ViewParticipant;
use \mywishlist\view\ViewErreur as ViewErreur;

class ControllerParticipant {

    private $c;

    public function __construct(\Slim\Container $c){
        $this->c = $c;
    }

    public function getListeDestinataire($rq, $rs, $args){
        try{
            //on compte le nombre de parametres passés dans l'URL
            $numberParam = count(explode('&', $_SERVER['QUERY_STRING']));
            
            //suivant le nombre de paramètres on affiche soit
            //0 -> page erreur
            //1 -> liste si le token existe
            //2 -> item si le numéro et le token sont cohérents
            //default -> page erreur
            switch($numberParam){
                case 1:
                    $t = $rq->getQueryParam('token');
                    if($t === NULL){
                        $a = $this->affiche_page_erreur($rq, $rs, $args);
                        return $a;
                    }else{
                        $htmlvars = ['basepath'=>$rq->getUri()->getBasePath()];
                        $elem = -1;
                        $liste = Liste::all();
                        $items = Item::all();
                        $trouve = false;
                        $nbr_liste = 0;
                        while($trouve === false && $nbr_liste <= count($liste)){
                            if($liste[$nbr_liste]->token === $t){
                                $trouve = true;
                                $elem = $nbr_liste;
                            }
                            $nbr_liste = $nbr_liste + 1;
                        }
                        $v = new ViewParticipant([$liste, $elem, $items]);
                        if($elem === -1){
                            $v = new ViewErreur(NULL); 
                            $rs->getBody()->write($v->render($htmlvars));
                            //$rs->getBody()->write($v->renderPageErreur($htmlvars));
                        }else{
                            $rs->getBody()->write($v->render($htmlvars));
                        }
                        return $rs;
                    }
                    break;
                case 2:
                    $t = $rq->getQueryParam('token');
                    $i = $rq->getQueryParam('items');
                    if( $t === NULL || $i === NULL ){
                        $a = $this->affiche_page_erreur($rq, $rs, $args);
                        return $a;
                    }
                    //on parcours les token pour savoir s'il existe
                    $liste = Liste::all();
                    $liste_trouve = -1;
                    for($j=0; $j<= count($liste); $j++){
                        if($liste[$j]->token === $t){
                            $liste_trouve = $liste[$j]->no;
                        }
                    }
                    if($liste_trouve === -1){
                        $a = $this->affiche_page_erreur($rq, $rs, $args);
                        return $a;
                    }else{
                        //on regarde si l'item qu'on a es dans la liste
                        $item = Item::query()->where('id', '=', $i)->firstOrFail();
                        //on regarde si l'item passé en parametre a pour liste
                        //la liste qui a pour token le token en parametre
                        if($liste_trouve === $item->liste_id){
                            $htmlvars = ['basepath'=>$rq->getUri()->getBasePath()];
                            $v = new ViewParticipant([$item]);
                            $rs->getBody()->write($v->renderItem($htmlvars));
                            return $rs;
                        }else{
                            $a = $this->affiche_page_erreur($rq, $rs, $args);
                            return $a;
                        }
                    }
                    break;
                default:
                    $a = $this->affiche_page_erreur($rq, $rs, $args);
                    return $a;
                    break;
            }

        }catch(ModelNotFoundException $e){
            $rs->getBody()->write("exception");
            return $rs;
        }
    }

    public function affiche_page_erreur($rq, $rs, $args){
        $htmlvars = ['basepath'=>$rq->getUri()->getBasePath()];
        $v = new ViewErreur(NULL); 
        $rs->getBody()->write($v->render($htmlvars));
        return $rs;
    }

}
