<?php

// Ce controleur sert à manipuler des utilisateurs, chose que seul un admin peut faire !

final class ControleurUtilisateur
{
    public function listeAction()
    {
        $this->paginerAction();
    }

    public function editAction(Array $A_parametres)
    {
        $I_identifiantUtilisateur = $A_parametres[0];

        if (!$I_identifiantUtilisateur)
        {
            // l'identifiant est absent, inutile de continuer !
            // on renvoit vers l'action par défaut, en l'occurrence, la liste des utilisateurs
            BoiteAOutils::redirigerVers('');
        } else
        {
            // l'identifiant donné correspond t-il à une entrée en base ?

            try {
                $O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());
                $O_utilisateur = $O_utilisateurMapper->trouverParIdentifiant($I_identifiantUtilisateur);
            } catch (Exception $O_exception)
            {
                // L'identifiant passé ne correspond à rien...
                BoiteAOutils::redirigerVers('');
            }

            // Si l'on est ici c'est qu'on a tout ce qu'il nous faut (un utilisateur !)
            // Nous le passons à la vue correspondante
            Vue::montrer('utilisateur/edit/form', array('utilisateur' => $O_utilisateur));
        }
    }

    public function miseajourAction(Array $A_parametres)
    {
        $I_identifiantUtilisateur = $A_parametres[0];
        $S_login = $_POST['login'];
        // TODO: vérifications sur l'input, même si PDO nettoie derrière

        $O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());
        $O_utilisateur = $O_utilisateurMapper->trouverParIdentifiant($I_identifiantUtilisateur);

        if ($S_login != $O_utilisateur->donneLogin()) {
            $O_utilisateur->changeLogin($S_login);
            $O_utilisateurMapper->actualiser($O_utilisateur);
        }
        // on redirige vers la liste !
        BoiteAOutils::redirigerVers('utilisateur/paginer');
    }

    public function supprAction(Array $A_parametres)
    {
        $I_identifiantUtilisateur = $A_parametres[0];

        $O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());
        $O_utilisateur = $O_utilisateurMapper->trouverParIdentifiant($I_identifiantUtilisateur);
        $O_utilisateurMapper->supprimer($O_utilisateur);

        $O_listeur = new Listeur($O_utilisateurMapper);
        $O_paginateur = new Paginateur($O_listeur);
        $O_paginateur->changeLimite(Constantes::NB_MAX_UTILISATEURS_PAR_PAGE);

        // Je veux, à partir de l'identifiant de mon utilisateur, déterminer quelle était
        // sa page, afin de revenir dessus après suppression
        // Attention, ceci ne marche que si en base l'id = le rang de l'enregistrement
        $I_pageCible = 1;

        if ($I_identifiantUtilisateur > Constantes::NB_MAX_UTILISATEURS_PAR_PAGE)
        {
            $I_pageCible = ceil($I_identifiantUtilisateur / Constantes::NB_MAX_UTILISATEURS_PAR_PAGE);
        }

        $A_utilisateurs = array();

        while (!count($A_utilisateurs) && $I_pageCible > 0)
        {
            // Si j'efface le dernier de la page, je ne veux pas revenir sur sa page,
            // qui sera vide, mais sur la précédente !
            // Je dois éviter la boucle infinie si jamais j'éfface le dernier enregistrement !
            $A_utilisateurs = $O_paginateur->recupererPage($I_pageCible);
            $I_pageCible--;
        }

        $A_pagination = $O_paginateur->paginer();

        // voir ce qu'on met dans utilisateurs !
        Vue::montrer ('utilisateur/liste', array('utilisateurs' => $A_utilisateurs, 'pagination' => $A_pagination));
    }

    public function paginerAction(Array $A_parametres = null)
    {
        $I_page = isset($A_parametres[0]) ? $A_parametres[0] : 1;
        $O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());

        $O_listeur = new Listeur($O_utilisateurMapper);
        $O_paginateur = new Paginateur($O_listeur);
        $O_paginateur->changeLimite(Constantes::NB_MAX_UTILISATEURS_PAR_PAGE);

        // on doit afficher puis installer la pagination
        $A_utilisateurs = $O_paginateur->recupererPage($I_page);

        $A_pagination = $O_paginateur->paginer();

        // voir ce qu'on met dans utilisateurs !
        Vue::montrer ('utilisateur/liste', array('utilisateurs' => $A_utilisateurs, 'pagination' => $A_pagination));
    }
}