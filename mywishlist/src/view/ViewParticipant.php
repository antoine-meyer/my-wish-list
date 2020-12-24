<?php

namespace mywishlist\view;

use \mywishlist\models\Liste as Liste;

class ViewParticipant{

    private $model;

    public function __construct($m){
        $this->model = $m;
    }

    public function render(array $vars){
        //on recupere l'indice
        $indice = $this->model[1];
        //si l'indice est -1 alors on a pas une bonne URL
        if($indice !== -1){
            $content = $this->htmlUneListeEtItems($this->model[0][$indice], $vars);
        }
        //partie commune de la page
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
                $content
                <h2>Fin de la page</h2>
            </body>
        </html>
        END;
        return $html;
    }

    public function renderPageErreur(array $vars){
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

    public function renderItem(array $vars){
        $content = $this->htmlUnItem($this->model[0], $vars);

        //on recupère la liste qui contient l'item actuel
        $numListe = $this->model[0]->liste_id;
        //on récupère le token associé
        $tokenListe = Liste::query()->where('no', '=', $numListe)->firstOrFail()->token;
        //===> cela nous permet de gérer le bouton retour à la liste

        //partie commune de la page
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
                <a href='{$vars['basepath']}/liste?token={$tokenListe}' class='bouton_retour'>Retour à la liste</a>
                $content
                <h2>Fin de la page</h2>
            </body>
        </html>
        END;
        return $html;
    }

    private function htmlUnItem(\mywishlist\models\Item $item, array $v): string{
        //gestion de la réservation
        $reserve = "Non réservé";
        if($item->reserve != 0){
            $reserve = "Réservé";
        }
        //gestion de l'affichage
        $html = <<<END
        <section class="contentItemAlone">
            <h3><u>Numéro de l'item :</u> {$item->id}</h3>
            <h3><u>Nom de l'item :</u> {$item->nom}</h3>
            <h3><u>Description :</u> {$item->descr}</h3>
            <img src="/mywishlist/web/img/{$item->img}" height=100>
            <h3><u>Prix :</u> {$item->tarif}€</h3>
            <h3><u>État :</u> {$reserve}</h3>
        </section>
        <br>
        END;
        //retour de la fonction
        return $html;
    }

    private function htmlUnItemDansListe(\mywishlist\models\Item $item, array $v): string{
        //gestion de la réservation
        $reserve = "Non réservé";
        if($item->reserve != 0){
            $reserve = "Réservé";
        }

        //on recupère la liste qui contient l'item actuel
        $numListe = $item->liste_id;
        //on récupère le token associé
        $tokenListe = Liste::query()->where('no', '=', $numListe)->firstOrFail()->token;

        //gestion de l'affichage
        $html = <<<END
        <a href="{$v['basepath']}/liste?token={$tokenListe}&items={$item->id}">
            <section class="contentItem">
                <h3><u>Nom de l'item :</u> {$item->nom}</h3>
                <img src="/mywishlist/web/img/{$item->img}" height=100>
                <h3><u>État :</u> {$reserve}</h3>
            </section>
        </a>
        <br>
        END;
        //retour de la fonction
        return $html;
    }

    private function htmlUneListeEtItems(\mywishlist\models\Liste $liste, array $v): string{
        $html = <<<END
            <h2><u>La liste</u></h2>
            <section class="content">
                <h3><u>Numéro référence de la liste :</u> {$liste->no}</h3>
                <h3><u>Numéro de référence du créateur :</u> {$liste->user_id}</h3>
                <h3><u>Titre de la liste :</u> {$liste->titre}</h3>
                <h3><u>Description :</u> {$liste->description}</h3>
                <h3><u>Date d'expiration :</u> {$liste->expiration}</h3>
                <h2><u>Les items de la liste :</u></h2>
        END;  
        //on recupere les items
        $items = $this->model[2];
        //on check tous les items
        foreach($items as $i){
            //on affiche que ceux qui font parti de la liste demandée
            if($i->liste_id === $liste->no){
                $html = $html . $this->htmlUnItemDansListe($i, $v);
            }
        }
        $html = $html . <<<END
            </section>
        END;
        return $html;
    }
    /*
    private function formulaireDeReservationDunItem(): string{
        $html = <<<END
            <h3>Formulaire de réservation :</h3>
            <form id="" method="get" action="">
                <label>Nom du participant : </label>
                <input>
                <br>
                <label>Message destiné au destinataire du cadeau : </label>
                <input>
                <br>
                <label>Message ou commentaire destiné à l'ensemble des utilisateurs de la liste : </label>
                <input>
                <br>
                <button>Valider</button>
            </form>
        END;
        return $html;
    }*/

}