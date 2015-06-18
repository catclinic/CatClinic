<?php

// Cette classe qui gère la connexion est un design pattern Singleton,
// sans doute le plus décrié de tous !

class Connexion
{
    private static $_O_instance;

    private $_O_connexion; // C'est là que réside ma connexion PDO

    private $_A_erreurs;

    const PARAM_ENTIER = PDO::PARAM_INT;

    const PARAM_CARAC = PDO::PARAM_STR;

    private function __construct ($S_environnement)
    {
        $A_params = parse_ini_file(Constantes::DATABASE_CONFIG_FILE, true);

        if (!$A_params) { // parse_ini_file renvoie false en cas de paramètres érronnés
            throw new BaseDeDonneesException('Connexion impossible : les paramètres sont incorrects');
        }

        if ($A_params[$S_environnement]) {
            // j'écrase le tableau complet avec celui qui m'interesse
            $A_params = $A_params[$S_environnement];

            $S_fichierErreurs = Constantes::repertoireErreursBaseDeDonnees() . $A_params['cible'] . '.ini';

            if (!file_exists($S_fichierErreurs))  {
                throw new BaseDeDonneesException('Connexion impossible : le fichier des erreurs est absent'.$S_fichierErreurs);
            }

            $this->_A_erreurs = parse_ini_file($S_fichierErreurs, true);

            // j'exige qu'on me donne de l'UTF8 (regardez le dernier paramètre du constructeur PDO)
            $this->_O_connexion = new PDO($A_params['cible'] . ':host=' . $A_params['serveur'] . 
                        ';dbname=' . $A_params['basededonnees'],
                        $A_params['utilisateur'],
                        $A_params['motdepasse'],
                        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

            $this->_O_connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return;
        }

        throw new BaseDeDonneesException('Les paramètres pour l\'environnement "' . $S_environnement . '" n\'existent pas !');
    }

    static public function recupererInstance($S_environnement = 'dev')
    {
        if (!self::$_O_instance instanceof self)
        {
            self::$_O_instance = new self ($S_environnement);
        }

        return self::$_O_instance;
    }

    public function donneLien () {
        return self::$_O_instance->_O_connexion;
    }

    public function projeter ($S_requete, Array $A_params = null) {
        return $this->_retournerTableau ($this->_O_connexion->prepare($S_requete), $A_params);
    }

    public function inserer ($S_requete, Array $A_params)
    {
        $O_pdoStatement = $this->_O_connexion->prepare($S_requete);
        $this->_lierParametres($O_pdoStatement, $A_params);
        try {
            $O_pdoStatement->execute();
        } catch (PDOException $O_exception) {
            $A_erreurs = $O_pdoStatement->errorInfo();
            if ($this->_donneErreur('doublon') == $A_erreurs[1]) {
                // Tentative d'insertion d'un doublon
                preg_match_all("/'([^']+)'/", $O_exception->getMessage(), $A_resultat);

                $S_doublon_valeur = $A_resultat[1][0];
                $S_doublon_champ = $A_resultat[1][1];

                throw new BaseDeDonneesDoublonException("'$S_doublon_valeur' existe déjà dans le champ '$S_doublon_champ'");
            }
        }
        return $this->_O_connexion->lastInsertId();
    }

    public function modifier ($S_requete, Array $A_params)
    {
        $O_pdoStatement = $this->_O_connexion->prepare($S_requete);
        return $O_pdoStatement->execute($A_params);
    }

    private function _retournerTableau (PDOStatement $O_pdoStatement, Array $A_params = null)
    {
        $this->_lierParametres($O_pdoStatement, $A_params);
        $O_pdoStatement->execute();
        $A_tuples = array();

        if ($O_pdoStatement)
        {
            while ($O_tuple = $O_pdoStatement->fetch (PDO::FETCH_OBJ))
            {
                $A_tuples[] = $O_tuple;
            }
        }

        return $A_tuples;
    }

    private function _lierParametres (PDOStatement $O_pdoStatement, Array $A_params = null)
    {
        if (is_array($A_params))
        {
            foreach ($A_params as $M_cle => $M_param)
            {
                /*
                    si la clé $M_cle est un entier on lui ajoute 1
                    car le premier indice du tableau est à l'indice 0
                    tandis que le premier placeholder est à la position 1
                    sinon on ne la modifie pas
                */
                $M_cle = (is_integer($M_cle) ? ($M_cle + 1) : $M_cle);

                if (is_array($M_param))
                {
                    $M_valeur = $M_param[0];
                    $I_typeDeDonnee = $M_param[1];
                    $O_pdoStatement->bindValue($M_cle, $M_valeur, $I_typeDeDonnee);
                }
                else
                {
                    $O_pdoStatement->bindValue($M_cle, $M_param);
                }
            }
        }
    }

    private function _donneErreur($S_erreur) {
        return array_key_exists(strtolower($S_erreur), $this->_A_erreurs) ? $this->_A_erreurs[strtolower($S_erreur)] : null;
    }
}