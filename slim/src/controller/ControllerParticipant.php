<?php

namespace slim\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \slim\models\Item as Item;
use \slim\models\Liste as Liste;
use \slim\view\ViewParticipant as ViewParticipant;

class ControllerParticipant {

    private $c;

    public function __construct(\Slim\Container $c){
        $this->c = $c;
    }

    public function getItem(Request $rq, Response $rs, array $args):Response{
        try{
            $item = Item::query()->where('id', '=', $args['id'])->firstOrFail();
            $htmlvars = [
                'basepath'=>$rq->getUri()->getBasePath()
            ];
            $v = new ViewParticipant([$item]);
            $rs->getBody()->write($v->render($htmlvars));
            return $rs;
        }catch(ModelNotFoundException $e){
            $rs->getBody()->write("Item {$item->id} non trouvé");
            return $rs;
        }
    }

    public function getItems(Request $rq, Response $rs, array $args):Response{
        try{
            $item = Item::all();
            $htmlvars = [
                'basepath'=>$rq->getUri()->getBasePath()
            ];
            $v = new ViewParticipant([$item]);
            $rs->getBody()->write($v->render($htmlvars));
            return $rs;
        }catch(ModelNotFoundException $e){
            $rs->getBody()->write("exception");
            return $rs;
        }
    }

    public function getListe(Request $rq, Response $rs, array $args):Response{
        try{
            $liste = Liste::query()->where('no', '=', $args['id'])->firstOrFail();
            $item = Item::all();
            $htmlvars = [
                'basepath'=>$rq->getUri()->getBasePath()
            ];
            $v = new ViewParticipant([$liste, $item]);
            $rs->getBody()->write($v->render($htmlvars));
            return $rs;
        }catch(ModelNotFoundException $e){
            $rs->getBody()->write("Item {$liste->no} non trouvé");
            return $rs;
        }
    }

    public function getListes($rq, $rs, $args){
        try{
            $liste = Liste::all();
            $htmlvars = [
                'basepath'=>$rq->getUri()->getBasePath()
            ];
            $v = new ViewParticipant([$liste]);
            $rs->getBody()->write($v->render($htmlvars));
            return $rs;
        }catch(ModelNotFoundException $e){
            $rs->getBody()->write("exception");
            return $rs;
        }
    }

}