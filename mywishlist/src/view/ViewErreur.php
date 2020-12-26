<?php

namespace mywishlist\view;

class ViewErreur{

    private $model;

    public function __construct($m){
        $this->model = $m;
    }

    public function render(array $vars){
        $html = <<<END
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Application Wishlist</title>
                <link rel="stylesheet" href="{$vars['basepath']}/../web/css/style.css">
            </head>
            <body>
                <h1>Application Wishlist</h1>
                <h2><u>Participants</u></h2>
                <h3><u>ATTENTION :</u> Pas de liste de souhaits avec cette URL !</h3>
                <h2>Fin de la page</h2>
            </body>
        </html>
        END;
        return $html;
    }

}
