<?php

/**
 * vue qui gère la partie participant
 */

namespace mywishlist\view;

use \mywishlist\models\Liste as Liste;
use \mywishlist\models\CommentairesListes as CommentairesListes;

class ViewParticipant{

    private $model;

    public function __construct($m){
        $this->model = $m;
    }

    public function render(array $vars){

        $content = $this->htmlUneListeEtItems($this->model[0], $vars);
    
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

    public function renderItem(array $vars){
        $content = $this->htmlUnItem($this->model[0], $vars);

        //on recupère la liste qui contient l'item actuel
        $numListe = $this->model[0]->liste_id;
        //on récupère le token associé
        $tokenListe = Liste::where('no', '=', $numListe)->firstOrFail()->token;
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
        if($item->reserve != 0){
            $reserve = "Réservé par <i>".$item->participant."</i>";
            //gestion du message associé à la réservation ou non
            if($item->messageReservation != NULL){
                $messReser = "<h3><u>Message associé à la réservation :</u> $item->messageReservation</h3>";
            }else{
                $messReser = "<h3><u>Message associé à la réservation :</u> Pas de message associé.</h3>";
            }
        }else{
            $reserve = "Non réservé";
            $messReser = "";
        }
        //on regarde si l'item à une URL
        if($item->url !==NULL){
            $u = "<a href={$item->url} target=blanck>".$item->url."</a>";
        }else{
            $u = "aucune URL renseignée";
        }
        //gestion de l'affichage
        $html = <<<END
        <section class="contentItemAlone">
            <h3><u>Numéro de l'item :</u> {$item->id}</h3>
            <h3><u>Nom de l'item :</u> {$item->nom}</h3>
            <h3><u>Description :</u> {$item->descr}</h3>
            <h3><u>URL externe :</u> {$u}</h3>
            <img src="/mywishlist/web/img/{$item->img}" height=100>
            <h3><u>Prix :</u> {$item->tarif}€</h3>
            <h3><u>État :</u> {$reserve}</h3>
            $messReser
        </section>
        <br>
        END;
        //
        if($item->reserve == 0){
            $tokenDeLaListe = Liste::where('no', '=', $item->liste_id)->firstOrFail()->token;
            $html = $html . $this->formulaireDeReservationDunItem($tokenDeLaListe, $v, $item->id);
        }   
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
        <a href="{$v['basepath']}/items/{$item->id}?token={$tokenListe}">
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
                <h2><u>Associé un message à cette liste :</u></h2>
            </section>
        END;
        //formulaire pour laisser un message sur la liste
        $html = $html . $this->formulaireMessageSurUneListe($liste->token, $v);  
        //la liste des messages associés à la liste
        $html = $html . <<<END
            <h2><u>Les messages de la liste :</u></h2>
        END;
        
        //on récupère les messages pour cette liste dans la BDD
        //UTILISER LASSOCIATION
        $comms = CommentairesListes::all();
        $fin = 1;
        $compteur_vide = 0;
        while($fin <= count($comms)){
            $c = CommentairesListes::query()->where('id', '=', $fin)->firstOrFail();
            if($c['liste_id'] === $liste->no){
                $html = $html . <<<END
                <section class="contentItemAlone">
                    <p>Date : {$c->date}</p>
                    <p>Message : {$c->message}</p>
                </section>
                END;
            }else{
                $compteur_vide = $compteur_vide + 1;
            }
            $fin = $fin + 1;
        }
        //cas où on n'aurait une zone de message vide
        if($compteur_vide === count($comms)){
            $html = $html . <<<END
            <section class="contentItemAlone">
                <p>Encore aucun message pour cette liste ... soyez le premier ! </p>
                <p>REMPLISSEZ LE FORMULAIRE CI DESSUS !! </p>
            </section>
            END;
        }

        //
        $html = $html . <<<END
            <h2><u>Les items de la liste :</u></h2>
        END;
        
        //on recupere les items
        $items = $this->model[1];
        //si pas d'item alors message cool
        if(count($items) === 0){
            $html = $html . <<<END
                <p>Nous sommes désolés mais il n'y a pour le moment aucuns items associés à cette liste.</h2>
            END;
        }


        //on check tous les items
        foreach($items as $i){
            $html = $html . $this->htmlUnItemDansListe($i, $v);
        }
        return $html;
    }
    
    private function formulaireDeReservationDunItem(string $t, array $va, int $num): string{
        $ht = <<<END
            <section class="contentItemAlone">
                <h3><u>Formulaire de réservation :</u></h3>
                <form id="formReservCadeau" method="POST" action="{$va['basepath']}/items/{$num}?token={$t}">
                    <label>Nom du participant : </label>
                    <br>
                    <br>
                    <input type="text" name="nomReservantCadeau" value="" placeholder="Votre nom" required>
                    <br>
                    <br>
                    <label>Message destiné au destinataire du cadeau : </label>
                    <br>
                    <br>
                    <input type="text" name="messageDestinataireCadeau" value="" placeholder="Votre message">
                    <br>
                    <br>
                    <button type="submit" name="valider_message_cadeau" value="OK">Valider</button>
                </form>
            </section>
        END;
        return $ht;
    }

    private function formulaireMessageSurUneListe(string $a, array $va): string{
        $ht = <<<END
            <section class="contentItemAlone">
                <h3><u>Formulaire :</u></h3>
                <form id="formMessList" method="POST" action="{$va['basepath']}/liste?token={$a}">
                    <label>Ajouté un message ou un commentaire à la liste : </label>
                    <br>
                    <br>
                    <input type="text" name="contenuCommentaireListe" value="" placeholder="Petit message" required>
                    <br>
                    <br>
                    <button type="submit" name="valider_message_liste" value="OK" >Valider</button>
                </form>
            </section>
        END;
        return $ht;
    }

}