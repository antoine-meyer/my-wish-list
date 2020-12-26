<?php

namespace mywishlist\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ControllerCreateur {

    private $c;

    public function __construct(\Slim\Container $c){
        $this->c = $c;
    }

    public function getCompteCreateur($rq, $rs, $args){
        try{
            //
            $rs->getBody()->write("Vous etes un createur et vous etes sur votre compte");
            return $rs;
        }catch(ModelNotFoundException $e){
            $rs->getBody()->write("exception");
            return $rs;
        }
    }

}