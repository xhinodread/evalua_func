<?
class Usersperfils extends appModel{
	public $useDbConfig = 'dbEvaluaFunc';
	public $useTable = 'user_usersperfils';
	
		
	/*
	var $hasMany = array(
		'usersperfiles' => array(
			'className'=>'usersperfiles',
			'foreignKey'=>'id'
			
		)
	);
	*/
	
	/*
	var $hasOne = array(
		'Usersperfil' => array(
			'className' => 'Usersperfil',
			//'foreingKey' => 'user_id'
			'foreingKey' => false,
			'conditions' => array('Usersperfil.id = Usersperfils.userperfil_id ')
		)
	);
	*/
	
}
?>