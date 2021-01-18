<?php

namespace mywishlist\view;

class ViewAccueil{

    private $model;

    public function __construct($m){
        $this->model = $m;
    }

    public function renderPageAccueil(array $vars){
        $html = <<<END
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <title>Application Wishlist</title>
            <link rel='stylesheet' href='../web/css/styleAccueil.css'>
        </head>
        <body>
            <h1>Application Wishlist</h1>
            <h2><u>PAGE D'accueil</u></h2>
            <h3><u>Identification pour les créateurs de listes</u></h3>
            <form method='get' class='formuCrea' action=''>
                <div class=''>
                    <div class=''>
                        <input class='cham' title='E-mail ou identifiant' maxlength='320' type='text' placeholder='E-mail ou identifiant' autocorrect='off' spellcheck='false'>
                    </div>
                    <div class=''>
                        <input class='cham' title='Mdp' maxlength='320' type='password' placeholder='Mot de passe'>
                    </div>
                </div>
                <div class=''>    
                    <button class='sub' type='submit'>Se connecter</button>
                </div>
            </form>
            <h3><u>Créer un compte de créateur de listes</u></h3>
            <p>A..</p>
        END;
        
        
        //gestion de la liste des listes de souhaits publiques
        //on récupère toutes les listes publiques
        $l = $this->model[0];
        //affiche le titre de la section
        $html = $html . <<<END
            <h3><u>Les listes de souhaits publiques </u></h3>
            <p>Note : Il n'y a pas de tri par date d'expiration croissante ...</p>
        END;
        //on parcours toutes les listes publiques et on affiche leur titre
        foreach($l as $li){
            $html = $html . <<<END
                <a href='/mywishlist/participants/liste?token={$li->token}'><u>Titre :</u> {$li->titre} - <u>Expiration :</u> {$li->expiration}</a>
                <br><br>
            END;
        }

        //gestion de la liste des créateurs 
        $html = $html . <<<END
            <h3><u>Liste des créateurs</u></h3>
            <p>A..</p>
        END;
        
        
        //fin de la page
        $html = $html . <<<END
            </body>
        </html>
        END;
        return $html;
    }

}