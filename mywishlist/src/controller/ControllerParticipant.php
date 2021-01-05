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
use \Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;

class ControllerParticipant {

    private $c;

    public function __construct(\Slim\Container $c){
        $this->c = $c;
    }

    public function getListeDestinataire($rq, $rs, $args){
        try{
            $htmlvars = ['basepath'=>$rq->getUri()->getBasePath()];
            $t = $rq->getQueryParam('token', null);
            $liste = Liste::where('token', '=', $t)->firstOrFail();
            $items = $liste->items()->get();
            $v = new ViewParticipant([$liste, $items]);
            $rs->getBody()->write($v->render($htmlvars));
        }catch(ModelNotFoundException $e){ 
            $mess = $this->affiche_page_erreur($rq, $rs, $args);
            return $mess;
        }
        return $rs;
    }



    public function affiche_page_erreur($rq, $rs, $args){
        $htmlvars = ['basepath'=>$rq->getUri()->getBasePath()];
        $v = new ViewErreur(NULL); 
        $rs->getBody()->write($v->render($htmlvars));
        return $rs;
    }



    public function getItem(Request $rq, Response $rs, array $args){
        try{
            $htmlvars = ['basepath'=>$rq->getUri()->getBasePath()];
            $t = $rq->getQueryParam('token', null);
            $liste = Liste::where('token', '=', $t)->firstOrFail();
            $item = Item::where('id', '=', $args['id'])->firstOrFail();
            if($item->liste_id === $liste->no){
                $v = new ViewParticipant([$item]);    
                $rs->getBody()->write($v->renderItem($htmlvars));
            }else{
                $mess = $this->affiche_page_erreur($rq, $rs, $args);
                return $mess;
            }
        }catch(ModelNotFoundException $e){ 
            $mess = $this->affiche_page_erreur($rq, $rs, $args);
            return $mess;
        }
        return $rs;
    }



    public function postFormulaireMessageListe($rq, $rs, $args){
        try{
            $t = $rq->getQueryParam('token', null);
            //$htmlvars = ['basepath'=>$rq->getUri()->getBasePath()."/liste?token={$t}"];
            $liste = Liste::where('token', '=', $t)->firstOrFail();

            $nouveauCommentaireSurLaListe = $rq->getParsedBody()['contenuCommentaireListe'];
            $liste_id = $liste->no;
            $date = date('Y-m-d');

            $query = filter_var($nouveauCommentaireSurLaListe, FILTER_SANITIZE_STRING);

            $message = new \mywishlist\models\CommentairesListes;
            $message->liste_id = $liste_id;
            $message->message = $query;
            $message->date = $date;
            $message->save();


            $rs = $this->getListeDestinataire($rq, $rs, $args);

            //return $response->withRedirect('/new-url', 301);

            //$rq->putUri("/ds");


        }catch(ModelNotFoundException $e){
            $mess = $this->affiche_page_erreur($rq, $rs, $args);
            return $mess;
        }
        return $rs;
    }
    

}
