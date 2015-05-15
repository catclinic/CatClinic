<?php

class FabriqueDexceptions
{
    private static $_A_exceptions = array(
        '23000' => array('DoublonException', 'Tentative d\'insertion d\'un doublon dans la table '),
    );

    public static function fabriquer($codeException, $source)
    {
        if (in_array($codeException, array_keys(self::$_A_exceptions))) {
            return new self::$_A_exceptions[$codeException][0](self::$_A_exceptions[$codeException][1] . $source);
        }
        throw new RuntimeException ("Aucune classe d'exceptions trouvée");
    }
}