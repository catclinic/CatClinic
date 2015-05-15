<?php

final class ControleurLogout
{
    public function defautAction()
    {
        // On efface notre utilisateur en session
        BoiteAOutils::rangerDansSession('utilisateur', null);
        // Retour à la case départ...il faut à nouveau se logger !
        BoiteAOutils::redirigerVers('login');
    }
}