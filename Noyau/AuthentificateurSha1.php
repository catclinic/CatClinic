<?php

final class AuthentificateurSha1 implements AuthentificateurInterface
{
    public function authentifier (Utilisateur $O_utilisateur, $S_motPasse)
    {   
        try {
            return ($O_utilisateur->donneMotDePasse () == BoiteAOutils::crypterMotDePasse($O_utilisateur, $S_motPasse));
        } catch (BadFunctionCallException $O_exception) {
            throw new AuthentificationException ($O_exception->getMessage());
        }
    }
}