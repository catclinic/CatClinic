<?php

class UtilisateurMapper extends SqlDataMapper
{
    public function __construct(Connexion $O_connexion)
    {
        parent::__construct(Constantes::TABLE_UTILISATEUR);
        $this->_S_classeMappee = 'Utilisateur';
        $this->_O_connexion = $O_connexion;
    }

    public function trouverParIntervalle ($I_debut, $I_fin) {
        ;
    }

    public function trouverParIdentifiant ($I_identifiant)
    {
        $S_requete    = "SELECT id, login, motdepasse, admin FROM " . $this->_S_nomTable .
                        " WHERE id = ?";
        $A_paramsRequete = array($I_identifiant);

        if ($A_utilisateur = $this->_O_connexion->projeter($S_requete, $A_paramsRequete))
        {
            // on sait donc qu'on aura 1 seul enregistrement dans notre tableau au max
            // c'est un objet de type stdClass
            $O_utilisateurTemporaire = $A_utilisateur[0];

             if (is_object($O_utilisateurTemporaire)) {
                if (class_exists($this->_S_classeMappee)) {
                    $O_utilisateur = new $this->_S_classeMappee;
                    $O_utilisateur->changeIdentifiant($O_utilisateurTemporaire->id);
                    $O_utilisateur->changeLogin($O_utilisateurTemporaire->login);
                    $O_utilisateur->changeAdmin($O_utilisateurTemporaire->admin);

                    return $O_utilisateur;
                }
            }

            throw new LogicException ('La classe "' . $this->_S_classeMappee . '" n\'existe pas');
        }
        else
        {
            // Je n'ai rien trouvé, je lève une exception pour le signaler au client de ma classe
            throw new Exception ("Il n'existe pas d'utilisateur d'identifiant '$I_identifiant'");
        }
    }

    public function trouverParLogin ($S_login)
    {
        $S_requete = "SELECT id, login, motdepasse, admin, id_proprietaire FROM " . $this->_S_nomTable . " WHERE login = ?";
        $A_paramsRequete = array($S_login);

        if ($A_utilisateur = $this->_O_connexion->projeter($S_requete, $A_paramsRequete))
        {
            // on sait donc qu'on aura 1 seul enregistrement dans notre tableau, car login est unique
            $O_utilisateurTemporaire = $A_utilisateur[0];
 
            if (class_exists($this->_S_classeMappee)) {
                $O_utilisateur = new $this->_S_classeMappee;
                $O_utilisateur->changeIdentifiant($O_utilisateurTemporaire->id);
                $O_utilisateur->changeLogin($O_utilisateurTemporaire->login);
                $O_utilisateur->changeMotDePasse($O_utilisateurTemporaire->motdepasse);
                $O_utilisateur->changeAdmin($O_utilisateurTemporaire->admin);
            }

            // je regarde si un propriétaire est relié à mon compte utilisateur
            // mais seulement si je ne suis pas admin

            if (!$O_utilisateur->estAdministrateur())
            {
                // Un utilisateur n'est pas forcément un propriétaire, mais s'il l'est
                // il faut récupérer ses données de propriétaire !
                try {
                    $O_proprietaireMapper = FabriqueDeMappers::fabriquer('proprietaire', $this->_O_connexion);
                    $O_proprietaire = $O_proprietaireMapper->trouverParIdentifiant($O_utilisateurTemporaire->id_proprietaire);
                } catch (Exception $O_exception) {
                    $O_proprietaire = null;
                }

                $O_utilisateur->changeProprietaire($O_proprietaire);
            }

            return $O_utilisateur;
        }
        else
        {
            throw new Exception ("Il n'existe pas d'utilisateur pour ce login");
        }
    }

    public function actualiser (Utilisateur $O_utilisateur)
    {
        if (!is_null($O_utilisateur->donneIdentifiant()))
        {
            if (!$O_utilisateur->donneLogin())
            {
                throw new Exception ("Impossible de lettre à jour l'utilisateur, des informations sont manquantes");
            }

            $S_login = $O_utilisateur->donneLogin();
            $I_identifiant = $O_utilisateur->donneIdentifiant();

            $S_requete   = "UPDATE " . $this->_S_nomTable . " SET login = ? WHERE id = ?";
            $A_paramsRequete = array($S_login, $I_identifiant);

            $this->_O_connexion->modifier($S_requete, $A_paramsRequete);

            return true;
        }

        return false;
    }

    // Attention : dans notre schéma de base de données, nous avons mis une clause de suppression de type
    // "cascade" au niveau de la table des propriétaires. Ce qui signifie qu'une suppression d'un utilisateur
    // entraine celle du propriétaire associé !

    public function supprimer (Utilisateur $O_utilisateur)
    {
        if (!is_null($O_utilisateur->donneIdentifiant()))
        {
            // il me faut absolument un identifiant pour faire une suppression
            $S_requete   = "DELETE FROM " . $this->_S_nomTable . " WHERE id = ?";
            $A_paramsRequete = array($O_utilisateur->donneIdentifiant());

            // si modifier echoue elle me renvoie false, si aucun enregistrement n'est supprimé, elle renvoie zéro
            // attention donc à bien utiliser l'égalité stricte ici !
            if (false === $this->_O_connexion->modifier($S_requete, $A_paramsRequete))
            {
                throw new Exception ("Impossible de supprimer l'utilisateur d'identifiant " . $O_utilisateur->donneIdentifiant());
            }

            return true;
        }

        return false;
    }

    public function creer (Utilisateur $O_utilisateur)
    {
        /* A FAIRE
        try {
            $O_utilisateur->changeIdentifiant($this->_O_connexion->inserer($S_requete, $A_paramsRequete));
        } catch (BaseDeDonneesDoublonException $O_exception) {
            // On propage l'exception en la faisant "remonter" (bubble up)
            throw new SQLDataMapperException("Doublon détecté : " . $O_exception->getMessage());
        }
        */
    }
}