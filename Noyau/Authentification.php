<?php

final class Authentification
{
    public static function estConnecte()
    {
        return !is_null(BoiteAOutils::recupererDepuisSession('utilisateur'));
    }
    
    public static function estAdministrateur()
    {
        // pour qu'un utilisateur soit admin il faut qu'il soit loggué ET admin
        return self::estConnecte() && BoiteAOutils::recupererDepuisSession('utilisateur')->estAdministrateur();
    }
    
    public static function authentifier (Utilisateur $O_utilisateur, $S_motdePasse, $S_algorithme = 'sha1')
    {
        $O_authentificateur = self::fabrique($S_algorithme);
        return $O_authentificateur->authentifier($O_utilisateur, $S_motdePasse);
    }
    
    protected static function fabrique($S_algorithme)
    {
        // mon usine a fabriquer des authentificateurs...
        // on lui donne le type d'authentificateur souhaité et elle le retourne (s'il existe...évidemment !)
        $S_type  = ucfirst(strtolower($S_algorithme));
        $S_classe= "Authentificateur" . $S_type;

        if (class_exists($S_classe)) {
            return new $S_classe;
        } else {
            throw new AuthentificationException ($S_type . ' n\'est pas un module d\'authentification valide.');    
        }
    }
}