<?php

$S_erreur = BoiteAOutils::recupererDepuisSession('erreur', false); // on veut la détruire après affichage, d'où le false

print '<span class="error">' . $S_erreur . '</span>';