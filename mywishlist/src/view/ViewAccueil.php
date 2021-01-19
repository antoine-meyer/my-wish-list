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
            <form method='post' class="formuCrea" action=''>
                <div class=''>
                    <div class=''>
                        <input class='cham' name="identifiant" title='Identifiant' maxlength='320' type='text' placeholder='Identifiant' autocorrect='off' spellcheck='false' required>
                    </div>
                    <div class=''>
                        <input class='cham' name="mot_de_passe" title='Mdp' maxlength='320' type='password' placeholder='Mot de passe' required>
                    </div>
                </div>
                <div class=''>    
                    <button class='sub' name="bouton_connectionCreateur" type='submit'>Se connecter</button>
                </div>
            </form>
            <br>
            <h3><u>Créer un compte de créateur de listes</u></h3>
            <form method='post' action=''>
                <input name="newIden" type='text' placeholder='Créer un identifiant' autocorrect='off' spellcheck='false' required>
                <input name="newMDP" type='password' placeholder='Créer un mot de passe' required>
                <input name="newMDP_confirmation" type='password' placeholder='Confirmer votre mot de passe' required>  
                <button name="bouton_creationDeCompte" type='submit'>Créer un compte</button>
            </form>
            <br>
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