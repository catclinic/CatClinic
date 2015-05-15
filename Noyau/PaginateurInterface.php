<?php

interface PaginateurInterface
{
    public function changeLimite ($I_limite);
    public function paginer ();
    public function recupererPage ($I_numeroPage);
    public function changeListeur (ListeurInterface $O_listeur);
}