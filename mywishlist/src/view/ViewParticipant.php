<?php

namespace mywishlist\view;

class ViewParticipant{

    private $model;

    public function __construct($m){
        $this->model = $m;
    }

    public function render(array $vars){
        //LISTES
        if(get_class($this->model[0][0])==="mywishlist\models\Liste" || get_class($this->model[0])==="mywishlist\models\Liste"){
            //liste unique et ses items
            if(sizeof($this->model[0])===1){
                $content = $this->htmlUneListeEtItems($this->model[0]);
            }else{
                //liste de listes
                foreach($this->model[0] as $l){
                    $content = $content .  $this->htmlUneListe($l);
                    $stateListes = "active";
                }
            }
        }
        //ITEMS
        else{
            //item unique
            if(sizeof($this->model[0])===1){
                $content = $this->htmlUnItem($this->model[0]);
            }else{
                //liste d'items
                foreach($this->model[0] as $l){
                    $content = $content .  $this->htmlUnItem($l);
                    $stateItems = "active";
                }
            }
        }
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
                <div class="topnav">
                    <a href="{$vars['basepath']}/../app/">Home</a>
                    <a class="$stateListes" href="{$vars['basepath']}/listes">Listes</a>
                    <a class="$stateItems" href="{$vars['basepath']}/items">Items</a>
                </div>
                $content
            </body>
        </html>
        END;
        return $html;
    }

    private function htmlUnItem(\mywishlist\models\Item $item): string{
        $html = <<<END
            <section class="content">
                <h3>Item {$item->id} : {$item->nom}</h3>
                <p>Description : {$item->descr}</p>
            </section>
        END;
        return $html;
    }

    private function htmlUneListe(\mywishlist\models\Liste $liste): string{
        $html = <<<END
            <section class="content">
                <h3>Liste {$liste->no} : {$liste->titre}</h3>
                <p>Description : {$liste->description}</p>
            </section>
        END;
        return $html;
    }

    private function htmlUneListeEtItems(\mywishlist\models\Liste $liste): string{
        $html = <<<END
            <section class="content">
                <h3>Liste {$liste->no} : {$liste->titre}</h3>
                <p>Description : {$liste->description}</p>
                <h3>Les items de cette liste sont : </h3>
        END;  
        //OPTIMISATION A REVOIR JE PENSE ICI !!!!
        //on check tous les items
        foreach($this->model[1] as $l){
            //on affiche que ceux qui font parti de la liste demandée
            if($l->liste_id === $liste->no){
                $html = $html . $this->htmlUnItem($l);
            }
        }
        $html = $html . <<<END
            </section>
        END;
        return $html;
    }
}