<?php

final class AuthentificateurStandard implements Authentificateur
{
    public function authentifier (Utilisateur $O_utilisateur, $S_motPasse)
    {    
        return ($O_utilisateur->donneMotDePasse () == BoiteAOutils::crypterMotDePasse($O_utilisateur, $S_motPasse));
    }
}