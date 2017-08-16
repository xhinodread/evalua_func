<?php
class User extends AppModel {
	public $useDbConfig = 'dbEvaluaFunc';
	var $name = 'User';
	////var $useTable = '';
	
	
	var $hasMany = array(
		'Usersperfils' => array(
			'className' => 'Usersperfils',
			'foreignKey' => 'user_id'
			//'foreingKey' => false,
			//'conditions' => array('Usersperfils.user_id = User.id ')
		)
	);
	

	public $validate  = array(
		'username' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Debe ingresar el nombre de Usuario',
					'allowEmpty' => false,
					'required' => true,
				)
				,'isUnique' => array(
					'rule' => array('isUnique'),
					'message' => 'Este nombre de usuario ya ha sido asignado.'
				)
		),
		'password' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Debe ingresar una clave',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
				)
		)
	);
	
	
}
?>