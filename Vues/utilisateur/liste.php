<h1>Utilisateurs</h1>
<?php
    // si une erreur s'est produite, elle remonte ici
    Vue::montrer('standard/erreurs');
?>
<table>
<caption>Liste des utilisateurs actifs du site</caption>
<thead>
    <tr>
        <td>Identifiant</td>
        <td>Login</td>
        <td>Administrateur</td>
    </tr>
</thead>
<?php

if (count($A_vue['utilisateurs']))
{
    echo '<tbody>';

    foreach ($A_vue['utilisateurs'] as $O_utilisateur)
    {
        // Allez, on ressort echo, print...
        print '<tr>';
        echo '<td>'. $O_utilisateur->donneIdentifiant() . '</td><td>' . 
                     $O_utilisateur->donneLogin() . '</td><td>' .
                    ($O_utilisateur->estAdministrateur() ? 'oui' : 'non') . '</td>';

        $O_utilisateurCourant = BoiteAOutils::recupererDepuisSession('utilisateur');

        if ($O_utilisateurCourant) {
            if ($O_utilisateur->donneLogin() != $O_utilisateurCourant->donneLogin()) {
                // On ne peut pas s'auto-supprimer ni même se modifier alors qu'on est connecté !
                print '<td><a href="/utilisateur/suppr/' . $O_utilisateur->donneIdentifiant() .
                    '" onclick="return(confirm(\'Etes-vous sûr de vouloir supprimer cet utilisateur ?\'));">
                    Effacer</a></td>';
                echo '<td><a href="/utilisateur/edit/' . $O_utilisateur->donneIdentifiant() . '">Modifier</a></td>';
            }
        } else {
            // Pas d'utilisateur courant en session, c'est qu'elle a expirée, on renvoie à l'accueil
            BoiteAOutils::redirigerVers('login');
        }
        echo '</tr>';
    }

    echo '</tbody>';
}
?>
</table>
<?php
    if (isset($A_vue['pagination']))
    {
        echo '<div>';
        foreach ($A_vue['pagination'] as $I_numeroPage => $S_lien)
        {
            echo '&nbsp;' . ($S_lien ? '<a href="/' . $S_lien . '">' . $I_numeroPage . '</a>' : $I_numeroPage);
        }
        echo '</div>';
    }
?>