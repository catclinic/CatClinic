<?php

interface FabriqueDeMappersInterface
{
    public static function fabriquer ($S_nom, Connexion $O_connexion, $S_type = 'Sql');
}