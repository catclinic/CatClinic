<?php

final class FabriqueDeMappers implements FabriqueDeMappersInterface
{
    public static function fabriquer ($S_nom, Connexion $O_connexion, $S_type = 'Sql') {
        $S_classeCible = ucfirst(strtolower($S_nom)) . 'Mapper';
        $S_repertoireCible = Constantes::repertoireMappers() .  ucfirst(strtolower($S_type));

        if (is_dir($S_repertoireCible)) {
            if (class_exists($S_classeCible)) {
                return new $S_classeCible ($O_connexion);
            }
        }

        throw new InvalidArgumentException ('Impossible de trouver un mapper nommé "' . $S_nom . '"');
    }
}