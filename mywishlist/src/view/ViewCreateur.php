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
            <p><b>Titre : </b>{$liste->titre}</p>
            <p><b>Description : </b>{$liste->description}</p>
            <p><b>Expiration : </b>{$liste->expiration}</p>
        END;


        //modifications des informations générales de la liste
        $a = $a . <<<END
            <h2><u>modifications des informations</u></h2>
            <p>Remplisser ce formulaire pour modifier les informations générales de la liste :</p>
            <section class="formulaire-liste">
                <form id="" method="POST" action="">
                    <input type="text" name="title" value="" placeholder="Le titre">
                    <input type="text" name="description" value="" placeholder="La description">
                    <input type="date" name="date" value="" placeholder="La date">
                    <button type="submit" name="bouton_modifierListe" value="">Modifier</button>
                </form>
            </section>
        END;


        //listes des items de la liste
        $a = $a . "<h2><u>LES ITEMS DE LA LISTE</u></h2>";
        //on récupère les items associés
        $items = $liste->items()->get();
        //on affiche les items
        //s'il n'y a pas d'items alors on affiche un joli message
        if(count($items) === 0){
            $a = $a . <<<END
                <p>Pas encore d'items ! Ajouter en avec le formulaire ci dessous !</p>
            END;
        }else{
            $a = $a . "<p>======</p>";
            foreach($items as $i){
                $a = $a . <<<END
                    <p><b>Nom : </b>{$i->nom}</p>
                    <p><b>Description : </b>{$i->descr}</p>
                    <p><b>Prix : </b>{$i->tarif}€</p>
                END;
                //si l'URL externe existe alors on l'affiche ici
                if($i->url !== NULL){
                    $a = $a . <<<END
                        <p><b>URL externe : </b><a class="lienExterne" href={$i->url} target=blanck>{$i->url}</a></p>
                    END;
                }else{
                    $a = $a . <<<END
                        <p><b>URL externe : </b>aucune URL renseignée</p>
                    END;
                }
                //on regarde si l'item est réservé on ne peut plus le modifier
                $c = "";
                if($i->reserve === 1){
                    $c = "<p>L'item est <b>réservé</b> : vous ne pouvez plus le modifier ni le supprimer.</p>";
                }else{
                    $c = $c . <<<END
                        <p>L'item n'est <b>pas encore réservé</b> !</p>
                        <p>Modifier l'item :
                            <a class='boutonModifier' href="{$vars['basepath']}/items/{$i->id}?token={$liste->tokenDeModification}">Modifier</a>
                        </p>
                        <form id="" method="POST" action="">
                            <label>Supprimer l'item de la liste : </label>
                            <button type="submit" name="bouton_supprimerItem" value="{$i->id}">Supprimer</button>
                        </form>
                    END;
                }
                //on assemble le tout 
                $a = $a . <<<END
                    $c
                    <p>======</p>
                END;
            }
        }
        

        //ajout d'un item avec formulaire de création d'un item
        $a = $a . <<<END
            <h2><u>Ajouter un item</u></h2>
            <p>Remplissez ce formulaire pour ajouter un item à cette liste.</p>
            <p>PAS DE PANIQUE si vous avez mal remplit : vous pouvez toujours modifier les informations d'un item non réservé.</p>
            <section class="formulaire-liste">
                <form id="" method="POST" action="">
                    <label>Nom : </label>
                    <input type="text" name="newItem_Nom" value="" placeholder="Le nom de l'item" required>
                    <br>
                    <label>Description : </label>
                    <input type="text" name="newItem_Description" value="" placeholder="La description de l'item" required>
                    <br>
                    <label>Prix : </label>
                    <input type="number" name="newItem_Prix" value="" placeholder="Le prix de votre item" required>
                    <br>
                    <p>Vous pouvez fournir l'URL d'une page externe qui détaille le produit (champ optionnel).</p>
                    <p>L'URL doit ressembler à : <b>https://exemple.com</b></p>
                    <label>URL page externe : </label>
                    <input type="url" name="newItem_URL" value="" placeholder="URL page externe">
                    <br>
                    <button type="submit" name="bouton_newItem" value="">Ajouter</button>
                </form>
            </section>
        END;


        //bouton pour partager la liste !
        //on partage que si la liste n'est pas déjà partagée donc on le vérifie
        $a = $a . <<<END
            <h2><u>Partager cette liste</u></h2>
        END;
        if($liste->token !== NULL){
            $a = $a . <<<END
                <p>Liste déjà partagée !</p>
                <p>Allez voir cette URL : {$_SERVER['HTTP_HOST']}/mywishlist/participants/liste?token={$liste->token}</p>
                <!--<button type="" name="" value="" >Ne plus partager la liste</button>-->
            END;
        }else{
            $a = $a . <<<END
                <p>Cette liste n'est pas encore partagée ... Voulez vous le faire ?</p>
                <form id="" method="POST" action="">
                    <button type="submit" name="bouton_partagerListe" value="" >Partager la liste</button>
                </form>
            END;
        }
        

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



    public function renderUnItem(array $vars){

        //variable pour stocker l'item
        $it = $this->model[0];

        //id de la liste associée à l'item
        $id = $it->liste_id;
        //token de la liste avec cette id
        $liste = $it->liste()->get()[0];
        $t = $liste->tokenDeModification;

        //bouton retour vers la liste associée
        $a = $a . <<<END
            <a href='{$vars['basepath']}/liste/{$id}?token={$t}' class=''>
                <button type="" name="" value="OK">Retour vers la liste associée</button>
            </a>
        END;

        //on récupère le compte de la liste
        $compte = $liste->compte()->get()[0];
        $id = $compte->id;
        $t = $compte->tokenmodification;

        //bouton retour vers le compte associée à la liste de cette item
        $a = $a . <<<END
            <a href='{$vars['basepath']}/compte/{$id}?token={$t}' class=''>
                <button type="" name="" value="OK">Retour vers le compte</button>
            </a>
        END;
            
        //informations sur l'item
        $a = $a . <<<END
            <h2><u>Informations sur l'item</u></h2>
            <p><b>Nom :</b> {$it->nom}</p>
            <p><b>Description :</b> {$it->descr}</p>
            <p><b>Prix :</b> {$it->tarif}€</p>
        END;
        //si on n'a pas d'url
        if($it->url === NULL){
            $a = $a . <<<END
            <p><b>Url externe :</b> aucune</p>
            END;
        }else{
            $a = $a . <<<END
            <p><b>Url externe :</b> {$it->url}</p>
            END;
        }
        //si on n'a pas d'img
        if($it->img === NULL){
            $a = $a . <<<END
            <p><b>Image :</b> aucune</p>
            END;
        }else{
            $a = $a . <<<END
            <p><b>Image :</b> {$it->img}</p>
            <img src="/mywishlist/web/img/{$it->img}" height=100>
            <p>BOUTON SUPPRIME NON FONCTIONNEL</p>
            <button type="" name="" value="">Supprimer l'image</button>
            END;
        }

        //formulaire pour modifier les informations
        $a = $a . <<<END
            <h2><u>Modifier l'item</u></h2>
            <p>Remplisser ce formulaire pour modifier les informations générales de l'item :</p>
            <section class="">
                <form id="" method="POST" action="">
                    <input type="" name="" value="" placeholder="Nouveau nom"><br>
                    <input type="" name="" value="" placeholder="Nouvelle description"><br>
                    <input type="" name="" value="" placeholder="Nouveau prix"><br>
                    <input type="" name="" value="" placeholder="Nouvelle URL externe"><br>
                    <button type="submit" name="" value="">Modifier</button>
                </form>
            </section>
        END;

        //rajouter une image à l'item
        $a = $a . <<<END
            <h2><u>Rajouter ou modifier une image à l'item</u></h2>
            <section class="">
                <form id="" method="POST" action="">
                    <p>Pour ajouter une image à l'item ou la modifier vous avez 2 possibilitées :</p>
                    <p><i>- fournir une URL d'une image externe ou</i></p>
                    <p><i>- fournir le chemin relatif d'une image présente dans le dossier web/img</i></p>
                    <input type="" name="" value="" placeholder="Modifier / Nouvelle image"><br>
                    <button type="submit" name="" value="">Modifier</button>
                </form>
            </section>
        END;

        //reponse finale
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
                <p><u>Liste partagée dont l'URL est :</u> {$_SERVER['HTTP_HOST']}/mywishlist/participants/liste?token={$listes[$i]->token}</p>
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
