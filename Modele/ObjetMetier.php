<?php

abstract class ObjetMetier {
	protected $_A_operations = array('supprimer' => array(), 'ajouter' => array());
	const SUPPRESSION = 'supprimer';
	
	public function donneOperations($type) {
		if (in_array($type, array_keys($this->_A_operations))) {
			return $this->_A_operations[$type];
		}
	}
}