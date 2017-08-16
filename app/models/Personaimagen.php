<?php
//App::uses('AppModel', 'Model');

class Personaimagen extends AppModel {
	public $useDbConfig = 'msSqlPersonas';
	public $name = 'Personaimagen';
	public $useTable = 'PER_IMAGEN';
	//public $primaryKey = 'id_per';
	//public $displayField = 'nombres';
	
	
	public $belongsTo = array(
		'Persona' => array(
				'className' => 'Persona',
				'foreignKey' => 'id_per'
		)
	);
		
}