<?php

/**
 * vue qui affiche
 */

namespace mywishlist\view;

class ViewCreateur{

    private $model;

    public function __construct($m){
        $this->model = $m;
    }

    public function renderListes(array $vars){
        //on recupere les listes
        $listes = $this->model[1];
        //information sur le createur
        $a = "<h2><u>Vos informations</u></h2>";
        $a = $a . "<p>Vous êtes <b>{$this->model[0]->nom}</b></p>";
        $a = $a . "<p>Votre mot de passe est <b>{$this->model[0]->password}</b></p>";
        $nbr_liste = count($listes);
        $a = $a . "<p>Vous avez <b>{$nbr_liste}</b> liste(s).</p>";

        //pour chaque liste
        $a = $a . "<h2><u>Vos listes</u></h2>";
        for($i=0; $i<count($listes); $i++){
            $num = $i + 1;
            $a = $a . <<<END
            <a href="{$vars['basepath']}/liste/{$listes[$i]->no}">
                <section class="une-boite-de-liste">
                    <p><b>Liste numéro $num</b></p>
                    <p><u>Titre :</u> {$listes[$i]->titre}</p>
                    <p><u>Description :</u> {$listes[$i]->description}</p>
                    <p><u>Date d'expiration :</u> {$listes[$i]->expiration}</p>
                    <p><u>URL à partager :</u> /mywishlist/participants/liste?token={$listes[$i]->token}</p>
                </section>
            </a>
            <br>
            END;
        }

        //bouton pour créer une liste
        $a = $a . "<h2><u>Créer une nouvelle liste</u></h2>";
        $a = $a . "BOUTON +";

        //on assemble le tout et on renvoie
        $html = <<<END
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Application Wishlist</title>
                <link rel="stylesheet" href="{$vars['basepath']}/../web/css/styleCreateurs.css">
            </head>
            <body>
                <h1>Application Wishlist</h1>
                <h2><u>Créateurs</u></h2>
                $a
            </body>
        </html>
        END;
        return $html;
    }


    public function renderUneListe(array $vars){

    }

}
