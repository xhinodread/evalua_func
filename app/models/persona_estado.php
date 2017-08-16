<?php
//App::uses('AppModel', 'Model');

class PersonaEstado extends AppModel {
	public $useDbConfig = 'msSqlPersonas';
	public $primaryKey = 'id_per';
	public $useTable = 'persona_estado';
	
	
	function getidfuncionario($userName = null){
		$resultado = $this->find('first', array('conditions'=>array('usuario' => $userName) ) );
		return $resultado['PersonaEstado']['id_per'];
	}
	
	/*
	public $validate = array(
		'per_rut' => array(
			'notEmpty'=> array('rule'=>'notEmpty'),
			'numeric'=> array('rule'=>'numeric', 'message'=>'Ingrese solo números'),
			'unique'=> array('rule'=>'isUnique', 'message'=>'Este rut ya esxiste!!!, Verifique.')
		)
	);
	*/
	/*
	public $hasOne = array(
		'persona' => array(
			'className' => 'Persona',
			'foreignKey' => 'id_per',
			'conditions' => '',
			'dependent'    => false
			)
		);
	*/
/*	
	var $hasOne = array(
        'Perfil' => array(
            'className'    => 'Perfil',
            'conditions'   => array('Perfil.publicado' => '1'),
            'dependent'    => true
        )
    );
*/
/*
	var $belongsTo= array(
		'User' => array(
			'className' => 'User',
			'foreingKey' => false,
			'conditions' => array('User.username = PersonaEstado.usuario')
		)
	);
*/

}
/*
per_rut
per_estado
cod_grupo
funcional
id_per
id_per_yomi
usuario
honorario
calidadJuridica
*/