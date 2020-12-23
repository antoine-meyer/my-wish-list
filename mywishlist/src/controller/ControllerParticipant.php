<?php

namespace mywishlist\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \mywishlist\models\Item as Item;
use \mywishlist\models\Liste as Liste;
use \mywishlist\view\ViewParticipant as ViewParticipant;

class ControllerParticipant {

    private $c;

    public function __construct(\Slim\Container $c){
        $this->c = $c;
    }

    public function getListeDestinataire($rq, $rs, $args){
        try{
            //on configure
            $htmlvars = [
                'basepath'=>$rq->getUri()->getBasePath()
            ];
            //on recupère le token qui nous interesse
            $t = $rq->getQueryParam('token');
            
            //on recupere toutes les listes
            $liste = Liste::all();
            //on recupere tous les items
            $items = Item::all();

            //si on a pas de token on met la page erreur
            if($t===NULL){
                $elem = -1;
                $v = new ViewParticipant([$liste, $elem, $items]);
                $rs->getBody()->write($v->render($htmlvars));
                return $rs;
            }

            //on regarde si une liste a ce token
            $trouve = false;
            $nbr_liste = 0;
            //indice dans la liste ou -1
            $elem = -1;
            //on parcours les listes
            while($trouve === false && $nbr_liste <= count($liste)){
                if($liste[$nbr_liste]->token === $t){
                    $trouve = true;
                    $elem = $nbr_liste;
                }
                $nbr_liste = $nbr_liste + 1;
            }
            //
            $v = new ViewParticipant([$liste, $elem, $items]);
            $rs->getBody()->write($v->render($htmlvars));
            return $rs;
        }catch(ModelNotFoundException $e){
            $rs->getBody()->write("exception");
            return $rs;
        }
    }

    public function getItem(Request $rq, Response $rs, array $args):Response{
        try{
            $item = Item::query()->where('id', '=', $args['id'])->firstOrFail();
            $htmlvars = [
                'basepath'=>$rq->getUri()->getBasePath()
            ];
            $v = new ViewParticipant([$item]);
            $rs->getBody()->write($v->renderItem($htmlvars));
            return $rs;
        }catch(ModelNotFoundException $e){
            $rs->getBody()->write("Item {$item->id} non trouvé");
            return $rs;
        }
    }
    
}