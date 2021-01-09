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

    public function renderUneListe(array $vars){
        //on récupère la liste
        $liste = $this->model[0];
        //on récupère le compte associé
        $compte = $liste->compte()->get()[0];

        //bouton retour vers le compte du créateur de cette liste
        //on récupère l'id du compte/user 
        $id = $compte->id;
        //et le token de modification associé
        $t = $compte->tokenmodification; 
        $a = <<<END
            <a href='{$vars['basepath']}/compte/{$id}?token={$t}' class=''>
                <button type="" name="" value="OK">Retour vers le compte</button>
            </a>
        END;


        //informations de la liste et modification possible
        $a = $a . <<<END
            <h2><u>informations de la liste</u></h2>
            <p>Titre : {$liste->titre}</p>
            <p>Description : {$liste->description}</p>
            <p>Expiration : {$liste->expiration}</p>
        END;

        //modifications des informations générales de la liste
            //<p>formualire poiur modifier les informations, aucun champs n'est required</p>
        $a = $a . <<<END
            <h2><u>modifications des informations</u></h2>
            <p>Remplissez ce </p>
            <section class="formulaire-liste">
                <form id="" method="" action="">
                    <label>CHAMP A FAIRE : </label><br><br><br><br><br><br>
                    <button type="" name="" value="OK">Modifier</button>
                </form>
            </section>
        END;

        //listes des items de la liste
            //il y a un bouton modifier qui envoie vers /item/x?token=... avec une page juste informations de l'item et formulaire pour modifier l'item et un bouton retour
            //il y a un bouton supprimer
        $a = $a . <<<END
            <h2><u>LES ITEMS DE LA LISTE</u></h2>
            <p>Item 1</p>
            <button type="" name="" value="OK">Modifier</button>
            <button type="" name="" value="OK">Supprimer</button>
            <br><br>
            <p>Item 2</p>
            <button type="" name="" value="OK">Modifier</button>
            <button type="" name="" value="OK">Supprimer</button>
            <br><br>
            <p>Item blabla</p>
            <button type="" name="" value="OK">Modifier</button>
            <button type="" name="" value="OK">Supprimer</button>
            <br><br>
        END;

        //ajout d'un item avec formulaire de création d'un item
            //nom
            //prix
            //url pour image
        $a = $a . <<<END
            <h2><u>Ajouter un item</u></h2>
            <p>Remplissez ce formulaire pour ajouter un item à cette liste. Si vous avez mal remplit pas d'inquiétude : vous pouvez toujours modifier les informations d'un l'item.</p>
            <section class="formulaire-liste">
                <form id="" method="" action="">
                    <label>CHAMP A FAIRE : </label><br><br><br><br><br><br>
                    <button type="" name="" value="OK">Ajouter</button>
                </form>
            </section>
        END;


        //bouton pour partager la liste !
        $a = $a . <<<END
            <h2><u>Partager cette liste</u></h2>
            <button type="" name="" value="" >Partager la liste</button>
        END;

        //D'AUTRES TRUCS VOIR SUJET !

        //page générale
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
                <h2>Fin de page</h2>
            </body>
        </html>
        END;
        return $html;
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
            //si on a un token alors liste public sinon liste privé
            if($listes[$i]->token !== NULL){
                $partage = <<<END
                <p><u>Liste partagée dont l'URL est :</u> /mywishlist/participants/liste?token={$listes[$i]->token}</p>
                END;
            }else{
                $partage = <<<END
                <p><b>Liste non partagée ...</b></p>
                END;
            }
            
            //résultat
            $a = $a . <<<END
            <a href="{$vars['basepath']}/liste/{$listes[$i]->no}?token={$listes[$i]->tokenDeModification}">
                <section class="une-boite-de-liste">
                    <p><b>Liste numéro $num</b></p>
                    <p><u>Titre :</u> {$listes[$i]->titre}</p>
                    <p><u>Description :</u> {$listes[$i]->description}</p>
                    <p><u>Date d'expiration :</u> {$listes[$i]->expiration}</p>
                    $partage
                </section>
            </a>
            <br>
            END;
        }

        //cas où il n'y aurait pas encore de liste
        if(count($listes) === 0){
            $a = $a . <<<END
                <p>Vous n'avez pas encore de liste ...</p>
                <p>Pas de panique, vous pouvez en créer une avec le bouton ci dessous !</p>
            END;
        }

        //formulaire pour créer une liste
        $numCompte = $this->model[0]->id;
        $t = $this->model[0]->tokenmodification;
        $a = $a . <<<END
        <h2><u>Créer une nouvelle liste</u></h2>
        <section class="formulaire-liste">
            <p><b><u>Formulaire de création d'une nouvelle liste :</u></b></p>
            <form id="" method="POST" action="{$vars['basepath']}/compte/{$numCompte}?token={$t}">
                    <label>Titre : </label>
                    <br>
                    <br>
                    <input type="text" name="tit" value="" placeholder="Le titre" required>
                    <br>
                    <br>
                    <label>Description : </label>
                    <br>
                    <br>
                    <input type="text" name="des" value="" placeholder="Votre description" required>
                    <br>
                    <br>
                    <label>Date d'échéance de la liste de souhaits : </label>
                    <br>
                    <br>
                    <input type="date" name="dat" value="" placeholder="La date" required>
                    <br>
                    <br>
                    <button type="submit" name="val" value="OK">Créer liste</button>
                </form>
        </section>
        END;

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
                <h2>Fin de page</h2>
            </body>
        </html>
        END;
        return $html;
    }



}
