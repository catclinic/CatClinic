<?php

if (Authentification::estConnecte())
{
    $A_liens = array('/' => 'Accueil');
    
    if (Authentification::estAdministrateur()) {
        $A_liens['/utilisateur/liste'] = 'Utilisateurs';
    }

    $A_liens['/logout'] = 'Déconnexion';
    
    echo '<ul style="float:left">';

    foreach ($A_liens as $S_lien => $S_titre) {
        echo '<li><a href="' . $S_lien . '">' . $S_titre . '</a></li>';
    }

    echo '</ul>';

    echo '<div class="message">' . 'Vous êtes connecté(e) en tant que ' . '<strong>' . BoiteAOutils::recupererDepuisSession('utilisateur')->donneLogin() . '</strong></div>';
}