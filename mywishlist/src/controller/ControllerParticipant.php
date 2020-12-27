<?php

/**
 * contrôleur pour le projet de visualisation ou destinataires de liste de souhaits
 */

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
            $t = $rq->getQueryParam('token', null);
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
                if($elem === -1){
                    $v = new ViewErreur(NULL); 
                    $rs->getBody()->write($v->render($htmlvars));
                }else{
                    //AMELIORATION : NE PAS METTRE TOUS LES ITEMS MAIS QUE CEUX QUI NOUS INTERESSENT ET DU COUP MODIF DANS LA VUE AUSSI
                    $v = new ViewParticipant([$liste, $elem, $items]);
                    $rs->getBody()->write($v->render($htmlvars));
                }
                return $rs;
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

    public function getItem(Request $rq, Response $rs, array $args):Response{
        try{
            //on recupere le token
            $t = $rq->getQueryParam('token', null);
            if($t === NULL){
                $a = $this->affiche_page_erreur($rq, $rs, $args);
                return $a;
            }else{
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
                    $item = Item::query()->where('id', '=', $args['id'])->firstOrFail();
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
            }
        }catch(ModelNotFoundException $e){
            $rs->getBody()->write("Item {$item->id} non trouvé");
            return $rs;
        }
    }

}
