<?php
// TODO
// tests unitaires...à compléter !!!

require '../Noyau/ChargementAuto.php';

$O_connexion = Connexion::recupererInstance();

$O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', $O_connexion);

// Cas de test numéro 1
// Test de la méthode trouverParIntervalle
// But : récupérer N utilisateurs dans un intervalle donné
// prenons N = 1

$A_resultat = $O_utilisateurMapper->trouverParIntervalle(1,1);

// On vérifie qu'on a bien UN résultat

if (1 != count($A_resultat)) {
    die("Le cas de test 1 a échoué !" . PHP_EOL);
}

echo "Cas de test 1 OK", PHP_EOL;

// Cas de test numéro 2
// Test de la méthode trouverParIdentifiant

$O_resultat = $O_utilisateurMapper->trouverParIdentifiant(1);

if (1 != $O_resultat->donneIdentifiant()) {
    die("Le cas de test 2 a échoué !" . PHP_EOL);
}

echo "Cas de test 2 OK", PHP_EOL;

// Cas de test numéro 3
// Test de la méthode actualiser
// Scénario de test : on récupère l'utilisateur d'identifiant X
// On change la valeur de son champ Login
// On l'enregistre via actualiser
// On retire le même de la base de données et on vérifie que le champ a bien la valeur mise à jour
// $O_resultat contient cet utilisateur, ré-utilisons cette variable !

$S_login = $O_resultat->donneLogin();
$S_nouveauLogin = "Machintruc";
$O_resultat->changeLogin($S_nouveauLogin);

$O_utilisateurMapper->actualiser($O_resultat);

$O_resultatApresMaj = $O_utilisateurMapper->trouverParIdentifiant(1);

if ($S_nouveauLogin != $O_resultatApresMaj->donneLogin()) {
    die("Le cas de test 3 a échoué !" . PHP_EOL);
}

echo "Cas de test 3 OK", PHP_EOL;

// Je remets le login comme il était
$O_resultatApresMaj->changeLogin($S_login);
$O_utilisateurMapper->actualiser($O_resultatApresMaj);

