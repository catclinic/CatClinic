<?php

final class Paginateur implements PaginateurInterface {

    private $_O_listeur;

    private $_I_limite;

    private $_I_pageCourante;

    public function __construct (ListeurInterface $O_listeur) {
        $this->_O_listeur = $O_listeur;
    }

    public function changeListeur (ListeurInterface $O_listeur) {
        $this->_O_listeur = $O_listeur;
    }

    public function changeLimite ($I_limite)
    {
        $this->_I_limite = $I_limite;
    }

    public function paginer ()
    {
    }

    public function recupererPage ($I_numeroPage)
    {
    }

}