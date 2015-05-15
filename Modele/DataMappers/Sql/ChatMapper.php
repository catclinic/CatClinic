<?php

class ChatMapper extends SqlDataMapper
{
    public function __construct(Connexion $O_connexion)
    {
        parent::__construct(Constantes::TABLE_CHAT);
        $this->_S_classeMappee = 'Chat';
        $this->_O_connexion = $O_connexion;
    }

    public function trouverParIntervalle ($I_debut, $I_fin) {
        //TODO: A FAIRE
    }

    public function trouverParTatouage ($S_tatouage)
    {
        if (isset($S_tatouage)) {
            $S_requete    = "SELECT id, nom, age, tatouage FROM " . $this->_S_nomTable .
                            " WHERE tatouage = ?";
            $A_paramsRequete = array($S_tatouage);

            if ($A_chat = $this->_O_connexion->projeter($S_requete, $A_paramsRequete))
            {
                $O_chatTemporaire = $A_chat[0];

                if (is_object($O_chatTemporaire)) {
                    if (class_exists($this->_S_classeMappee)) {
                        $O_chat = new $this->_S_classeMappee;

                        $O_chat->changeIdentifiant($O_chatTemporaire->id);
                        $O_chat->changeNom($O_chatTemporaire->nom);
                        $O_chat->changeAge($O_chatTemporaire->age);
                        $O_chat->changeTatouage($O_chatTemporaire->tatouage);

                        return $O_chat;
                    }
                } else {
                    throw new Exception ("Une erreur s'est produite pour le chat dont le tatouage est '$S_tatouage'");
                }
            }
            else
            {
                // Je n'ai rien trouvé, je lève une exception pour le signaler au client de ma classe
                throw new Exception ("Il n'existe pas de chat dont le tatouage est '$S_tatouage'");
            }
        }
        else
        {
            throw new Exception ("Le tatouage d'un chat ne peut être vide");
        }  
    }

    public function trouverParIdentifiant ($I_identifiant)
    {
        if (isset($I_identifiant)) {
            $S_requete    = "SELECT id, nom, age, tatouage FROM " . $this->_S_nomTable .
                            " WHERE id = ?";
            $A_paramsRequete = array($I_identifiant);

            if ($A_chat = $this->_O_connexion->projeter($S_requete, $A_paramsRequete))
            {
                $O_chatTemporaire = $A_chat[0];

                if (is_object($O_chatTemporaire)) {
                    if (class_exists($this->_S_classeMappee)) {
                        $O_chat = new $this->_S_classeMappee;

                        $O_chat->changeIdentifiant($O_chatTemporaire->id);
                        $O_chat->changeNom($O_chatTemporaire->nom);
                        $O_chat->changeAge($O_chatTemporaire->age);
                        $O_chat->changeTatouage($O_chatTemporaire->tatouage);

                        return $O_chat;
                    }
                } else {
                    throw new Exception ("Une erreur s'est produite pour le chat d'identifiant '$I_identifiant'");
                }
            }
            else
            {
                // Je n'ai rien trouvé, je lève une exception pour le signaler au client de ma classe
                throw new Exception ("Il n'existe pas de chat pour l'identifiant '$I_identifiant'");
            }
        }
        else
        {
            throw new Exception ("L'identifiant d'un chat ne peut être vide et doit être un entier");
        }
    }

    public function creer (Chat $O_chat)
    {
        if (!$O_chat->donneNom() || !$O_chat->donneAge() || !$O_chat->donneTatouage())
        {
            throw new Exception ("Impossible d'enregistrer le chat, des informations sont manquantes");
        }

        $S_tatouage = $O_chat->donneTatouage();
        $S_nom = $O_chat->donneNom();
        $I_age = $O_chat->donneAge();

        $S_requete = "INSERT INTO " . $this->_S_nomTable . " (nom, age, tatouage) VALUES (?, ?, ?)";
        $A_paramsRequete = array($S_nom, $I_age, $S_tatouage);

        // j'insère en table et inserer me renvoie l'identifiant de mon nouvel enregistrement...je le stocke
        try {
            $O_chat->changeIdentifiant($this->_O_connexion->inserer($S_requete, $A_paramsRequete));
        } catch (PDOException $O_exception) {
            throw FabriqueDexceptions::fabriquer($O_exception->getCode(), $this->recupererCible());
        }
    }

    public function actualiser (Chat $O_chat)
    {
        if (null != $O_chat->donneIdentifiant())
        {
            if (!$O_chat->donneNom() || !$O_chat->donneAge() || !$O_chat->donneTatouage())
            {
                throw new Exception ("Impossible de mettre à jour le chat d'identifiant " . $O_chat->donneIdentifiant());
            }

            $S_tatouage = $O_chat->donneTatouage();
            $S_nom = $O_chat->donneNom();
            $I_age = $O_chat->donneAge();
            $I_identifiant = $O_chat->donneIdentifiant();

            $S_requete = "UPDATE " . $this->_S_nomTable . " SET nom = ?, tatouage = ?, age = ? WHERE id = ?";
            $A_paramsRequete = array($S_nom, $S_tatouage, $I_age, $I_identifiant);

            $this->_O_connexion->modifier($S_requete, $A_paramsRequete);

            return true;
        }

        return false;
    }

    public function supprimer (Chat $O_chat)
    {
        if (null != $O_chat->donneIdentifiant())
        {
            // il me faut absolument un identifiant pour faire une suppression
            $S_requete   = "DELETE FROM " . $this->_S_nomTable . " WHERE id = ?";
            $A_paramsRequete = array($O_chat->donneIdentifiant());

            // si modifier echoue elle me renvoie false, si aucun enregistrement n'est supprimé, elle renvoie zéro
            // attention donc à bien utiliser l'égalité stricte ici !
            if (false === $this->_O_connexion->modifier($S_requete, $A_paramsRequete))
            {
                throw new Exception ("Impossible de supprimer le chat d'identifiant " . $O_chat->donneIdentifiant());
            }

            return true;
        }

        return false;
    }
}