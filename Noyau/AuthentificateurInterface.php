<?php

// Toute classe qui prétend authentifier doit se conformer à cette interface !

interface AuthentificateurInterface
{
    public function authentifier (Utilisateur $O_utilisateur, $S_motPasse);
}